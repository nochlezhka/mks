<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ShelterRoomAdmin extends BaseAdmin
{
    protected $translationDomain = 'AppBundle';

    public function configure()
    {
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('number', TextType::class, [
                'label' => 'Номер комнаты',
                'required' => true,
            ])
            ->add('maxOccupants', IntegerType::class, [
                'label' => 'Максимальное количество жильцов',
                'required' => false,
            ])
            ->add('currentOccupants', IntegerType::class, [
                'label' => 'Текущее количество жильцов',
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Комментарий',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('number', TextType::class, [
                'label' => 'Номер комнаты',
            ])
            ->add('maxOccupants', IntegerType::class, [
                'label' => 'Максимальное количество жильцов',
            ])
            ->add('currentOccupants', IntegerType::class, [
                'label' => 'Текущее количество жильцов',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Комментарий',
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
