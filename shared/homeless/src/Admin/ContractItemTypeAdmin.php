<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ContractItemType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.contract_item_type.admin',
    'label' => 'contract_item_types',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => ContractItemType::class,
])]
final class ContractItemTypeAdmin extends AbstractAdmin
{
    use AdminTrait;

    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'name',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', null, [
                'label' => 'Название',
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
}
