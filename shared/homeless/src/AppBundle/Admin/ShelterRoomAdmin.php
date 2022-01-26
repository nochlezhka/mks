<?php

namespace AppBundle\Admin;

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ShelterRoomAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected $translationDomain = 'AppBundle';


    // public function configure()
    // {
    //     $this->parentAssociationMapping = 'client';
    // }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->add('save', 'save')
            ->add('post_edit', 'edit');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('number', null, [
                'label' => 'Номер комнаты'
            ])
            ->add('maxOccupants', null, [
                'label' => 'Максимальное кол-во жильцов'
            ])
            ->add('currentOccupants', null, [
                'label' => 'Текущее кол-во жильцов',
                'required' => false
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false
            ]);

        $formMapper->end();
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
        ;
    }
}