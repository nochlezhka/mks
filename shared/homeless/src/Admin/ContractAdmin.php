<?php

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Contract;
use App\Entity\User;
use App\Form\Type\AppContractDurationType;
use DateTime;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Сервисные планы',
    'model_class' => Contract::class,
    'controller'=> CRUDController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]

class ContractAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected string $translationDomain = 'App';

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('download', $this->getRouterIdParameter() . '/download');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with('contract');

        if ($this->getSubject()->getId() > 0) {
            $form
                ->add('duration', AppContractDurationType::class, ['label' => 'Долгосрочность', 'required' => false,])
                ->add('number', null, [
                    'label' => 'Номер',
                    'disabled' => true,
                    'attr' => array(
                        'readonly' => true,
                    )
                ]);
        }

        $form
            ->add('status', EntityType::class, [
                'label' => 'Статус',
                'required' => true,
                'class' => 'App\Entity\ContractStatus',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
            ])
            ->add('dateFrom', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата начала',
                'required' => true,
            ])
            ->add('dateTo', DatePickerType::class, [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата окончания',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
            ->end()
            ->with('contract_items')
            ->add('items', CollectionType::class, [
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
    protected function configureListFields(ListMapper $list): void
    {
        $list
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
            ->tokenStorage
            ->getToken()
            ->getUser();

        if(!($user instanceof User)) {
            throw new InvalidArgumentException("Unexpected User type");
        }

        if ($user->hasRole('ROLE_ADMIN') || $user->isSuperAdmin()) {
            $actions['delete'] = [];
        }

        $actions['download'] = [
            'template' => '/CRUD/list_contract_action_download.html.twig'
        ];

        $list->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
            'label' => 'Действие',
            'actions' => $actions,
        ]);
    }
}
