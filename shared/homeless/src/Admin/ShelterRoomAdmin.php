<?php

namespace App\Admin;

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class ShelterRoomAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    );

    protected string $translationDomain = 'App';

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->add('save', 'save')
            ->add('post_edit', 'edit');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
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

        $form->end();
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('number', null, [
                'label' => 'Название',
            ])
        ;
    }
}