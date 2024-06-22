<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Notice;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.notice.admin',
    'label' => 'notices',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => Notice::class,
])]
final class NoticeAdmin extends AbstractAdmin
{
    use AdminTrait;

    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'date',
    ];

    public function getById(
        ProxyQueryInterface $queryBuilder,
        string $alias,
        string $_,
        FilterData $data,
    ): bool {
        if (!$data->hasValue()) {
            return false;
        }

        $queryString = null;
        $valueCount = \count($data->getValue());
        $valueIndex = 0;
        foreach ($data->getValue() as $val) {
            ++$valueIndex;
            $orOperator = $valueIndex !== $valueCount ? 'OR ' : '';

            $queryString .= "{$alias}.id = {$val} {$orOperator}";
        }

        $queryBuilder->andWhere("({$queryString})");

        return true;
    }

    public function getViewedFilter(
        ProxyQueryInterface $queryBuilder,
        string $alias,
        string $_,
        FilterData $data,
    ): bool {
        if (!$data->hasValue()) {
            return false;
        }

        if ($data->getValue() === 1) {
            $queryBuilder->andWhere(':user MEMBER OF '.$alias.'.viewedBy');
        }

        if ($data->getValue() === 2) {
            $queryBuilder->andWhere(':user NOT MEMBER OF '.$alias.'.viewedBy');
        }

        $queryBuilder->setParameter('user', $this->getUser());

        return true;
    }

    public function prePersist(object $object): void
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('Unexpected User type');
        }

        if (!$object instanceof Notice) {
            return;
        }

        $this->processViewedBeforeSave($object, $user);
    }

    public function preUpdate(object $object): void
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('Unexpected User type');
        }

        if (!$object instanceof Notice) {
            return;
        }

        $this->processViewedBeforeSave($object, $user);
    }

    public function processViewedBeforeSave(Notice $notice, User $user): void
    {
        if ($notice->isViewed()) {
            if (!$notice->getViewedBy()->contains($user)) {
                $notice->addViewedBy($user);
            }
        } else {
            if ($notice->getViewedBy()->contains($user)) {
                $notice->removeViewedBy($user);
            }
        }
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('text', null, [
                'label' => 'Текст',
                'required' => true,
            ])
            ->add('date', DatePickerType::class, [
                'label' => 'Дата',
                'format' => 'dd.MM.yyyy',
                'required' => true,
                'input' => 'datetime_immutable',
            ])
            ->add('viewed', CheckboxType::class, [
                'label' => 'Просмотрено',
                'required' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('text', null, [
                'label' => 'Текст',
                'route' => ['name' => 'edit'],
            ])
            ->add('viewed', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'Просмотрено',
                'editable' => true,
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
                'admin_code' => 'sonata.user.admin.user',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('date', DateRangeFilter::class, [
                'label' => 'Дата',
                'advanced_filter' => false,
            ], [
                'field_type' => DateRangePickerType::class,
                'field_options' => [
                    'field_options_start' => [
                        'label' => 'От',
                        'format' => 'dd.MM.yyyy',
                    ],
                    'field_options_end' => [
                        'label' => 'До',
                        'format' => 'dd.MM.yyyy',
                    ],
                ],
            ])
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
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлено',
                'admin_code' => 'sonata.user.admin.user',
                'advanced_filter' => false,
            ])
            ->add('id', CallbackFilter::class, [
                'label' => 'id',
                'callback' => [$this, 'getById'],
                'field_type' => TextType::class,
                'advanced_filter' => false,
            ])
        ;
    }
}
