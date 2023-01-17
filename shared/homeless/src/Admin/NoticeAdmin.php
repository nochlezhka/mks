<?php

namespace App\Admin;

use App\Controller\ClientController;
use App\Entity\Client;
use App\Entity\Notice;
use App\Entity\User;
use InvalidArgumentException;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\NumberFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Напоминания',
    'model_class' => Notice::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]

class NoticeAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'date',
    );

    protected string $translationDomain = 'App';

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add(TextType::class, null, [
                'label' => 'Текст',
                'required' => true,
            ])
            ->add('date', DatePickerType::class, [
                'label' => 'Дата',
                'format' => 'dd.MM.yyyy',
                'required' => true,
            ])
            ->add('viewed', 'checkbox', [
                'label' => 'Просмотрено',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier(TextType::class, null, [
                'label' => 'Текст',
                'route' => ['name' => 'edit'],
            ])
            ->add('viewed', 'boolean', [
                'label' => 'Просмотрено',
                'editable' => true
            ])
            ->add('date', null, [
                'label' => 'Дата',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdAt', null, [
                'label' => 'Когда добавлено',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлено',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('date', DateRangeFilter::class, ['label' => 'Дата', 'advanced_filter' => false,],
                [
                    'field_options_start' => [
                        'label' => 'От',
                        'format' => 'dd.MM.yyyy'
                    ],
                    'field_options_end' => [
                        'label' => 'До',
                        'format' => 'dd.MM.yyyy'
                    ]
                ]
            )
            ->add('viewed', CallbackFilter::class, [
                    'label' => 'Просмотрено',
                    'callback' => [$this, 'getViewedFilter'],
                    'field_type' => ChoiceType::class,
                    'field_options' => [
                        'label' => ' ',
                        'choices' => [
                            'Да' => 1,
                            'Нет' => 2,
                        ],
                    ],
                    'advanced_filter' => false,
                ]
            )
            ->add('createdBy', NumberFilter::class, [
                    'label' => 'Кем добавлено',
                    'field_type' => TextType::class,
                    'advanced_filter' => false,
                ]
            )
            ->add('id', CallbackFilter::class, [
                    'label' => 'id',
                    'callback' => [$this, 'getById'],
                    'field_type' => TextType::class,
                    'advanced_filter' => false,
                ]
            );
    }

    public function getById(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $queryString = null;
        $valueCount = count($data->getValue());
        $valueIndex = 0;
        foreach ($data->getValue() as $val) {
            $valueIndex++;
            if ($valueIndex !== $valueCount) {
                $orOperator = 'OR ';
            } else {
                $orOperator = '';
            }

            $queryString .= "$alias.id=$val $orOperator";
        }

        $queryBuilder->andWhere("($queryString)");

        return true;

    }

    public function getViewedFilter(
        ProxyQueryInterface $queryBuilder, string $alias, string $field, FilterData $data
    ): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        if ($data->getValue() == 1) {
            $queryBuilder
                ->andWhere(':user MEMBER OF ' . $alias . '.viewedBy');
        }

        if ($data->getValue() == 2) {
            $queryBuilder
                ->andWhere(':user NOT MEMBER OF ' . $alias . '.viewedBy');
        }

        $queryBuilder->setParameter('user', $this->tokenStorage->getToken()->getUser());

        return true;
    }

    /**
     * @param mixed $object
     */
    public function prePersist($object): void
    {
        $this->tokenStorage
            ->getToken()
            ->getUser();

        $user = $this
            ->tokenStorage
            ->getToken()
            ->getUser();

        if(!($user instanceof User)) {
            throw new InvalidArgumentException("Unexpected User type");
        }

        $this->processViewedBeforeSave($object, $user);
    }

    /**
     * @param mixed $object
     */
    public function preUpdate($object): void
    {
        $user = $this
            ->tokenStorage
            ->getToken()
            ->getUser();

        if(!($user instanceof User)) {
            throw new InvalidArgumentException("Unexpected User type");
        }

        $this->processViewedBeforeSave($object, $user);
    }

    /**
     * @param Notice $notice
     * @param User $user
     */
    public function processViewedBeforeSave(Notice $notice, User $user)
    {
        if ($notice->getViewed()) {
            if (!$notice->getViewedBy()->contains($user)) {
                $notice->addViewedBy($user);
            }
        } else {
            if ($notice->getViewedBy()->contains($user)) {
                $notice->removeViewedBy($user);
            }
        }
    }
}
