<?php

namespace App\Admin;

use App\Controller\ShelterRoomController;
use App\Entity\ShelterRoom;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Приют',
    'model_class' => ShelterRoom::class,
    'controller'=> ShelterRoomController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]

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