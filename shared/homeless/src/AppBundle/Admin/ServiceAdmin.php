<?php

namespace AppBundle\Admin;

use AppBundle\Entity\ServiceType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ServiceAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected $translationDomain = 'AppBundle';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /* type */
        $typeOptions = [
            'required' => true,
        ];

        $em = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $availableCertTypes = $em
            ->getRepository('AppBundle:ServiceType')
            ->getAvailableForService($this->getSubject());

        $hasTypeWithComment = false;
        $hasTypeWithAmount = false;
        foreach ($availableCertTypes as $availableCertType) {
            $typeOptions['choices'][$availableCertType->getId()] = $availableCertType->getName();
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
            $formMapper->add('type', ChoiceFieldMaskType::class, $typeOptions);
            if ($hasTypeWithComment) {
                $formMapper->
                add('comment', 'text', [
                    'label' => 'Комментарий',
                    'required' => false,
                ]);
            }
            if ($hasTypeWithAmount) {
                $formMapper->add('amount', 'text', [
                    'label' => 'Сумма',
                    'required' => false,
                ]);
            }
        } else {
            $formMapper->add('type', ChoiceType::class, $typeOptions);
        }

        $transformer = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('app.service_type_to_choice_field_mask_type.transformer');

        $formMapper->getFormBuilder()->get('type')->addModelTransformer($transformer);

        $formMapper
            ->add('createdAt', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Когда добавлена',
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('type.name', null, [
                'label' => 'Тип',
            ]);

        if (!$this->isChild()) {
            $listMapper
                ->add('client', null, [
                    'label' => 'Клиент',
                    'route' => ['name' => 'show'],
                ]);
        }

        $listMapper
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
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('createdBy', null, [
                'label' => 'Кем добавлена',
                'advanced_filter' => false,
                'field_options' => [
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy("CONCAT(u.lastname,' ',u.firstname,' ',u.middlename)", 'ASC');
                    },
                    'class' => 'Application\Sonata\UserBundle\Entity\User',
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
                    'class' => 'AppBundle\Entity\ServiceType',
                ]
            ])
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                ['label' => 'Когда добавлена', 'advanced_filter' => false,],
                'Sonata\Form\Type\DateRangePickerType',
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

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        $result = $name;

        switch ($name){
            case 'edit':
                $name = 'AppBundle:Admin\Service:base_edit.html.twig';
                break;
            case 'outer_list_rows_list':
                $name = '@SonataAdmin/CRUD/list_outer_rows_list.html.twig';
                break;
            default:
                $name = parent::getTemplate($name);
                break;
        }

        return $name;
    }
}
