<?php

namespace AppBundle\Admin;

use AppBundle\Entity\ServiceType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;

class DeliveryAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected $translationDomain = 'AppBundle';

    public function configure()
    {
        $this->parentAssociationMapping = 'client';
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('deliveryItem.categoryName', null, ['label' => 'Категория'])
            ->add('deliveryItem.name', null, ['label' => 'Название']);

        if (!$this->isChild()) {
            $listMapper
                ->add('client', null, [
                    'label' => 'Клиент',
                    'route' => ['name' => 'show'],
                ]);
        }

        $listMapper
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
                    'delete' => [],
                ]
            ]);
    }

}
