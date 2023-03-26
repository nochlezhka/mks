<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\ResidentQuestionnaire;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'resident_questionnaire',
    'model_class' => ResidentQuestionnaire::class,
    'controller' => CRUDController::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
])]

class ResidentQuestionnaireAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'typeId',
    ];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('typeId', ChoiceType::class, [
                'label' => 'Тип',
                'choices' => ResidentQuestionnaire::$types,
                'required' => false,
            ])
            ->add('isDwelling', CheckboxType::class, [
                'label' => 'Проживает в жилом помещении',
                'required' => false,
            ])
            ->add('roomTypeId', ChoiceType::class, [
                'label' => 'Тип помещения',
                'choices' => ResidentQuestionnaire::$roomTypes,
                'required' => false,
            ])
            ->add('isWork', CheckboxType::class, [
                'label' => 'Работает?',
                'required' => false,
            ])
            ->add('isWorkOfficial', CheckboxType::class, [
                'label' => 'Официальная работа?',
                'required' => false,
            ])
            ->add('isWorkConstant', CheckboxType::class, [
                'label' => 'Постоянная работа?',
                'required' => false,
            ])
            ->add('changedJobsCountId', ChoiceType::class, [
                'label' => 'Сколько сменил работ',
                'choices' => ResidentQuestionnaire::$changedJobsCounts,
                'required' => false,
            ])
            ->add('reasonForTransitionIds', ChoiceType::class, [
                'label' => 'Причина перехода на другую работу',
                'multiple' => true,
                'choices' => ResidentQuestionnaire::$reasonForTransitions,
                'required' => false,
            ])
            ->add('reasonForPetitionIds', ChoiceType::class, [
                'label' => 'Причина обращения',
                'multiple' => true,
                'choices' => ResidentQuestionnaire::$reasonForPetition,
                'required' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('type', null, [
                'label' => 'Тип',
            ])
            ->add('isFull', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'Заполнено',
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
