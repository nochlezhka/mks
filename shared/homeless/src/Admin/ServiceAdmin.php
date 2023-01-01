<?php

namespace App\Admin;

use App\Entity\ServiceType;
use App\Form\DataTransformer\AdditionalFieldToArrayTransformer;
use App\Form\DataTransformer\ServiceTypeToChoiceFieldMaskTypeTransformer;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Service\Attribute\Required;

class ServiceAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected string $translationDomain = 'App';

    private ManagerRegistry $managerRegistry;

    private ServiceTypeToChoiceFieldMaskTypeTransformer $transformer;

    #[Required]
    public function setManager(ManagerRegistry $managerRegistry): void
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Required]
    public function setTransformer(ServiceTypeToChoiceFieldMaskTypeTransformer $transformer): void
    {
        $this->transformer = $transformer;
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
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
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
            ->add('createdBy.lastname', null, [
                'label' => 'Кем добавлена',
                'template' => '@App/Admin/Service/fields/_user_name.twig',
            ])
            ->add('_action', null, [
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
                'Sonata\Form\Type\DateRangePickerType',
                ['label' => 'Когда добавлена', 'advanced_filter' => false,],
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
            );
    }
}