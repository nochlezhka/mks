<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Entity\ClientFieldOption;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'choice_field_options',
    'model_class' => ClientFieldOption::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
])]
class ClientFieldOptionAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('notSingle', null, [
                'label' => 'Не может быть единственным ответом',
            ])
            ->add('sort', null, [
                'label' => 'Порядок сортировки',
                'required' => true,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        if (!$list->getAdmin()->isChild()) {
            $list->add('field');
        }

        $list
            ->addIdentifier('id', 'number')
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('notSingle', null, [
                'label' => 'Не может быть единственным ответом',
            ])
            ->add('sort', 'number', [
                'label' => 'Порядок сортировки',
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
