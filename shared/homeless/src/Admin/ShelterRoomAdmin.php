<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ShelterRoom;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.shelter_room.admin',
    'label' => 'shelter_rooms',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => ShelterRoom::class,
])]
final class ShelterRoomAdmin extends AbstractAdmin
{
    use AdminTrait;

    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateFrom',
    ];

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->add('save', 'save')
            ->add('post_edit', 'edit')
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('number', null, [
                'label' => 'Номер комнаты',
            ])
            ->add('maxOccupants', null, [
                'label' => 'Максимальное кол-во жильцов',
            ])
            ->add('currentOccupants', null, [
                'label' => 'Текущее кол-во жильцов',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('number', null, [
                'label' => 'Название',
            ])
            ->add('maxOccupants', TextType::class, [
                'label' => 'Максимальное кол-во жильцов',
            ])
            ->add('currentOccupants', TextType::class, [
                'label' => 'Текущее кол-во жильцов',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
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
