<?php

namespace AppBundle\Admin;

use AppBundle\Entity\DeliveryItem;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class DeliveryItemAdmin extends BaseAdmin
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
            ->add('category', 'choice', [
                'label' => 'Категория',
                'choices' => [
                    DeliveryItem::CATEGORY_CLOTHES => 'Одежда',
                    DeliveryItem::CATEGORY_HYGIENE => 'Гигиена',
                    DeliveryItem::CATEGORY_CRUTCHES => 'Другое',
                ],
            ])
            ->add('limitDays', 'number', [
                'label' => 'Лимит дней',
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
            ->add('category', 'choice', [
                'label' => 'Категория',
                'choices' => [
                    DeliveryItem::CATEGORY_CLOTHES => 'Одежда',
                    DeliveryItem::CATEGORY_HYGIENE => 'Гигиена',
                    DeliveryItem::CATEGORY_CRUTCHES => 'Другое',
                ],
            ])
            ->add('limitDays', 'number', [
                'label' => 'Лимит дней',
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
