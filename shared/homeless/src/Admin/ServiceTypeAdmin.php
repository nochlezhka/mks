<?php

namespace App\Admin;

use App\Entity\ServiceType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Типы услуг',
    'model_class' => ServiceType::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class ServiceTypeAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected string $translationDomain = 'App';

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('sort', TextType::class, [
                'label' => 'Сортировка',
                'required' => true,
            ])
            ->add('comment', 'checkbox', [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->add('amount', 'checkbox', [
                'label' => 'Сумма',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('sort', TextType::class, [
                'label' => 'Сортировка',
            ])
            ->add('comment', 'boolean', [
                'label' => 'Комментарий',
            ])
            ->add('amount', 'boolean', [
                'label' => 'Сумма',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }
}
