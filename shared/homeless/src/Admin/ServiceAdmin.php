<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Service;
use App\Entity\ServiceType;
use App\Entity\User;
use App\Form\DataTransformer\ServiceTypeToChoiceFieldMaskTypeTransformer;
use App\Repository\ServiceTypeRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.service.admin',
    'controller' => CRUDController::class,
    'label' => 'services',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => Service::class,
])]
class ServiceAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    public function __construct(
        private readonly ServiceTypeRepository $serviceTypeRepository,
        private readonly ServiceTypeToChoiceFieldMaskTypeTransformer $transformer,
    ) {
        parent::__construct();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $typeOptions = [
            'required' => true,
        ];

        $serviceTypes = $this->serviceTypeRepository->findBy([], ['sort' => 'ASC']);

        $hasTypeWithComment = false;
        $hasTypeWithAmount = false;
        /** @var \App\Entity\ServiceType $serviceType */
        foreach ($serviceTypes as $serviceType) {
            $typeOptions['choices'][$serviceType->getName()] = $serviceType->getId();
            $map = [];
            if ($serviceType->isComment()) {
                $map[] = 'comment';
                $hasTypeWithComment = true;
            }
            if ($serviceType->isAmount()) {
                $map[] = 'amount';
                $hasTypeWithAmount = true;
            }
            if (!empty($map)) {
                $typeOptions['map'][$serviceType->getId()] = $map;
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
                $form->add('comment', TextType::class, [
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
            ->add('createdAt', DatePickerType::class, [
                'dp_default_date' => (new \DateTimeImmutable())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Когда добавлена',
                'input' => 'datetime_immutable',
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('type.name', null, [
                'label' => 'Тип',
            ])
        ;

        if (!$this->isChild()) {
            $list
                ->add('client', null, [
                    'label' => 'Клиент',
                    'route' => ['name' => 'show'],
                ])
            ;
        }

        $list
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
            ->add('amount', null, [
                'label' => 'Сумма',
            ])
            ->add('createdAt', FieldDescriptionInterface::TYPE_DATE, [
                'label' => 'Когда добавлена',
                'pattern' => 'dd.MM.YYYY',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлена',
                'admin_code' => UserAdmin::class,
                'route' => ['Fullname' => 'show'],
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
            ->add('createdBy', null, [
                'label' => 'Кем добавлена',
                'admin_code' => UserAdmin::class,
                'advanced_filter' => false,
                'field_options' => [
                    'class' => User::class,
                    'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('u')
                        ->orderBy("CONCAT(u.lastname,' ',u.firstname,' ',u.middlename)", 'ASC'),
                ],
            ])
            ->add('type', null, [
                'label' => 'Тип',
                'advanced_filter' => false,
                'field_options' => [
                    'class' => ServiceType::class,
                    'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('t')
                        ->orderBy('t.sort', 'ASC'),
                ],
            ])
            ->add('createdAt', DateRangeFilter::class, [
                'label' => 'Когда добавлена',
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
        ;
    }
}
