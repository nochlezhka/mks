<?php

namespace App\Admin;

use App\Entity\ResidentQuestionnaire;
use App\Service\MetaService;
use App\Service\ResidentQuestionnaireConverter;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ResidentQuestionnaireAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'typeId',
    );

    protected $translationDomain = 'App';

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('typeId', ChoiceType::class, [
                'label' => 'Тип',
                'choices' => ResidentQuestionnaire::$types,
                'required' => false,
            ])
            ->add('isDwelling', 'checkbox', [
                'label' => 'Проживает в жилом помещении',
                'required' => false,
            ])
            ->add('roomTypeId', ChoiceType::class, [
                'label' => 'Тип помещения',
                'choices' => ResidentQuestionnaire::$roomTypes,
                'required' => false,
            ])
            ->add('isWork', 'checkbox', [
                'label' => 'Работает?',
                'required' => false,
            ])
            ->add('isWorkOfficial', 'checkbox', [
                'label' => 'Официальная работа?',
                'required' => false,
            ])
            ->add('isWorkConstant', 'checkbox', [
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

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('type', null, [
                'label' => 'Тип',
            ])
            ->add('isFull', 'boolean', [
                'label' => 'Заполнено',
            ]);
        $list
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return ResidentQuestionnaireConverter
     */
    private function getConverter()
    {
        return $this->getConfigurationPool()->getContainer()->get('app.resident_questionnaire_converter');
    }

    /**
     * @return MetaService
     */
    private function getMetaService()
    {
        return $this->getConfigurationPool()->getContainer()->get('app.meta_service');
    }
}
