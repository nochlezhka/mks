<?php

namespace AppBundle\Admin;

use AppBundle\Form\Type\AppContractDurationType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ContractAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected $translationDomain = 'AppBundle';

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Сервисный план');

        if ($this->getSubject()->getId() > 0) {
            $formMapper
                ->add('duration', AppContractDurationType::class, ['label' => 'Долгосрочность', 'required' => false,])
                ->add('number', null, [
                    'label' => 'Номер',
                    'disabled' => true,
                    'attr' => array(
                        'readonly' => true,
                    )
                ]);
        }

        $formMapper
            ->add('status', 'entity', [
                'label' => 'Статус',
                'required' => true,
                'class' => 'AppBundle\Entity\ContractStatus',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
            ])
            ->add('dateFrom', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'view_timezone' => $this->getParameter('admin_view_timezone'),
                'label' => 'Дата начала',
                'required' => true,
            ])
            ->add('dateTo', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new \DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата окончания',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
            ->end()
            ->with('Пункты')
            ->add('items', 'sonata_type_collection', [
                'label' => false,
                'required' => true,
                'by_reference' => false,
                'type_options' => [
                    'delete' => true,
                ]
            ], [
                'edit' => 'inline',
                'inline' => 'table',
            ])
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('duration', 'number', [
                'template' => '/admin/fields/contract_duration_list.html.twig',
                'label' => ' ',
            ])
            ->addIdentifier('number', null, [
                'label' => 'Номер',
            ])
            ->add('status', null, [
                'label' => 'Статус',
            ])
            ->add('dateFrom', null, [
                'label' => 'Дата начала',
            ])
            ->add('dateTo', null, [
                'label' => 'Дата окончания',
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлен',
            ]);

        $actions = [
            'edit' => [],
        ];
        $user = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.token_storage')
            ->getToken()
            ->getUser();

        if ($user->hasRole('ROLE_ADMIN') || $user->isSuperAdmin()) {
            $actions['delete'] = [];
        }

        $actions['download'] = [
            'template' => '/CRUD/list_contract_action_download.html.twig'
        ];

        $listMapper->add('_action', null, [
            'label' => 'Действие',
            'actions' => $actions,
        ]);
    }
}
