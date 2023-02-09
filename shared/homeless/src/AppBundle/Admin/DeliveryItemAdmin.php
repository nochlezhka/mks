<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Branch;
use AppBundle\Entity\DeliveryItem;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
                    DeliveryItem::CATEGORY_CLOTHES => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_CLOTHES],
                    DeliveryItem::CATEGORY_HYGIENE => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_HYGIENE],
                    DeliveryItem::CATEGORY_CRUTCHES => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_CRUTCHES],
                    DeliveryItem::CATEGORY_REHABILITATION => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_REHABILITATION],
                    DeliveryItem::CATEGORY_DISHES => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_DISHES],
                    DeliveryItem::CATEGORY_GIFT => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_GIFT],
                ],
            ])
            ->add('branches', null, [
                'label' => 'Город',
                'multiple' => true,
                'by_reference' => false

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
                    DeliveryItem::CATEGORY_CLOTHES => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_CLOTHES],
                    DeliveryItem::CATEGORY_HYGIENE => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_HYGIENE],
                    DeliveryItem::CATEGORY_CRUTCHES => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_CRUTCHES],
                    DeliveryItem::CATEGORY_REHABILITATION => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_REHABILITATION],
                    DeliveryItem::CATEGORY_DISHES => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_DISHES],
                    DeliveryItem::CATEGORY_GIFT => DeliveryItem::$CATEGORY_NAMES[DeliveryItem::CATEGORY_GIFT],
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
