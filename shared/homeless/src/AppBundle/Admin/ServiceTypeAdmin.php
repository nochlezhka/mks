<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ServiceTypeAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'sort',
    );

    protected $translationDomain = 'AppBundle';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('sort', 'text', [
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
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('sort', 'text', [
                'label' => 'Сортировка',
            ])
            ->add('comment', 'boolean', [
                'label' => 'Комментарий',
            ])
            ->add('amount', 'boolean', [
                'label' => 'Сумма',
            ])
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }
}
