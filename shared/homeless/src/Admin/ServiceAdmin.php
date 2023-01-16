<?php

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Service;
use App\Entity\ServiceType;
use App\Form\DataTransformer\ServiceTypeToChoiceFieldMaskTypeTransformer;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'services',
    'model_class' => Service::class,
    'controller' => CRUDController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class ServiceAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected string $translationDomain = 'App';

    private ManagerRegistry $managerRegistry;

    private ServiceTypeToChoiceFieldMaskTypeTransformer $transformer;

    public function __construct(ManagerRegistry $managerRegistry, ServiceTypeToChoiceFieldMaskTypeTransformer $transformer)
    {
        $this->managerRegistry = $managerRegistry;
        $this->transformer = $transformer;
        parent::__construct();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        /* type */
        $typeOptions = [
            'required' => true,
        ];

        $em = $this->managerRegistry->getManager();

        $availableCertTypes = $em
            ->getRepository(ServiceType::class)
            ->getAvailableForService($this->getSubject());

        $hasTypeWithComment = false;
        $hasTypeWithAmount = false;
        foreach ($availableCertTypes as $availableCertType) {
            $typeOptions['choices'][$availableCertType->getName()] = $availableCertType->getId();
            $map = [];
            if ($availableCertType->getComment()) {
                $map[] = 'comment';
                $hasTypeWithComment = true;
            }
            if ($availableCertType->getAmount()) {
                $map[] = 'amount';
                $hasTypeWithAmount = true;
            }
            if (!empty($map)) {
                $typeOptions['map'][$availableCertType->getId()] = $map;
            }
        }
        $typeOptions['multiple'] = false;
        $typeOptions['label'] = 'Тип';
        $typeOptions['attr'] = [
            'class' => 'service_type_select',
            'data-sonata-select2' => 'true',
        ];

        if ($hasTypeWithComment || $hasTypeWithAmount) {
            $form->add('type', ChoiceFieldMaskType::class, $typeOptions);
            if ($hasTypeWithComment) {
                $form->
                add('comment', TextType::class, [
                    'label' => 'Комментарий',
                    'required' => false,
                ]);
            }
            if ($hasTypeWithAmount) {
                $form->add('amount', TextType::class, [
                    'label' => 'Сумма',
                    'required' => false,
                ]);
            }
        } else {
            $form->add('type', ChoiceType::class, $typeOptions);
        }

        $form->getFormBuilder()->get('type')->addModelTransformer($this->transformer);

        $form
            ->add('createdAt', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Когда добавлена',
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('type.name', null, [
                'label' => 'Тип',
            ]);

        if (!$this->isChild()) {
            $list
                ->add('client', null, [
                    'label' => 'Клиент',
                    'route' => ['name' => 'show'],
                ]);
        }

        $list
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
            ->add('amount', null, [
                'label' => 'Сумма',
            ])
            ->add('createdAt', 'date', [
                'label' => 'Когда добавлена',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлена',
                'route' => ['Fullname' => 'show']
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('createdBy', null, [
                'label' => 'Кем добавлена',
                'advanced_filter' => false,
                'field_options' => [
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy("CONCAT(u.lastname,' ',u.firstname,' ',u.middlename)", 'ASC');
                    },
                    'class' => 'App\Entity\User',
                ]
            ])
            ->add('type', null, [
                'label' => 'Тип',
                'advanced_filter' => false,
                'field_options' => [
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                            ->orderBy('t.sort', 'ASC');
                    },
                    'class' => 'App\Entity\ServiceType',
                ]
            ])
            ->add(
                'createdAt',
                DateRangeFilter::class,
                ['label' => 'Когда добавлена', 'advanced_filter' => false,],
                [
                    'field_type'=> DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'От',
                            'format' => 'dd.MM.yyyy'
                        ],
                        'field_options_end' => [
                            'label' => 'До',
                            'format' => 'dd.MM.yyyy'
                        ]
                    ]
                ]
            );
    }
}
