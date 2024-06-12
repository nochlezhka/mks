<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\Contract;
use App\Entity\ContractStatus;
use App\Entity\User;
use App\Form\Type\AppContractDurationType;
use App\Security\User\Role;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.contract.admin',
    'controller' => CRUDController::class,
    'label' => 'Сервисные планы',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => Contract::class,
])]
final class ContractAdmin extends AbstractAdmin
{
    use AdminTrait;

    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    ];

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('download', $this->getRouterIdParameter().'/download');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with('contract');

        if ($this->getSubject()->getId() > 0) {
            $form
                ->add('duration', AppContractDurationType::class, [
                    'label' => 'Долгосрочность',
                    'required' => false,
                ])
                ->add('number', null, [
                    'label' => 'Номер',
                    'disabled' => true,
                    'attr' => [
                        'readonly' => true,
                    ],
                ])
            ;
        }

        $form
            ->add('status', EntityType::class, [
                'label' => 'Статус',
                'required' => true,
                'class' => ContractStatus::class,
                'query_builder' => static fn (EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('s')
                    ->orderBy('s.name', 'ASC'),
            ])
            ->add('dateFrom', DatePickerType::class, [
                'datepicker_options' => [
                    'defaultDate' => (new \DateTimeImmutable())->format('Y-m-d'),
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата начала',
                'required' => true,
                'input' => 'datetime_immutable',
            ])
            ->add('dateTo', DatePickerType::class, [
                'datepicker_options' => [
                    'defaultDate' => (new \DateTimeImmutable())->format('Y-m-d'),
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата окончания',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
            ])
        ;
        $form->end();

        $form->with('contract_items');
        $form
            ->add('items', CollectionType::class, [
                'label' => false,
                'required' => true,
                'by_reference' => false,
                'type_options' => [
                    'delete' => true,
                ],
            ], [
                'edit' => 'inline',
                'inline' => 'table',
            ])
        ;
        $form->end();
    }

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
                'admin_code' => 'sonata.user.admin.user',
            ])
        ;

        $actions = [
            'edit' => [],
        ];

        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException('Unexpected User type');
        }

        if ($user->hasRole(Role::EMPLOYEE) || $user->hasRole(Role::SUPER_ADMIN)) {
            $actions['delete'] = [];
        }

        $actions['download'] = [
            'template' => '/CRUD/list_contract_action_download.html.twig',
        ];

        $list->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
            'label' => 'Действие',
            'actions' => $actions,
        ]);
    }
}
