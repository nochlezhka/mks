<?php

namespace AppBundle\Admin;

use AppBundle\Entity\ResidentQuestionnaire;
use AppBundle\Service\ResidentQuestionnaireConverter;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ResidentQuestionnaireAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'typeId',
    );

    protected $translationDomain = 'AppBundle';

    public function configure()
    {
        $this->parentAssociationMapping = 'client';
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('typeId', 'choice', [
                'label' => 'Тип',
                'choices' => ResidentQuestionnaire::$types,
                'required' => false,
            ])
            ->add('isDwelling', 'checkbox', [
                'label' => 'Проживает в жилом помещении',
                'required' => false,
            ])
            ->add('roomTypeId', 'choice', [
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
            ->add('changedJobsCountId', 'choice', [
                'label' => 'Сколько сменил работ',
                'choices' => ResidentQuestionnaire::$changedJobsCounts,
                'required' => false,
            ])
            ->add('reasonForTransitionIds', 'choice', [
                'label' => 'Причина перехода на другую работу',
                'multiple' => true,
                'choices' => ResidentQuestionnaire::$reasonForTransitions,
                'required' => false,
            ])
            ->add('reasonForPetitionIds', 'choice', [
                'label' => 'Причина обращения',
                'multiple' => true,
                'choices' => ResidentQuestionnaire::$reasonForPetition,
                'required' => false,
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('type', null, [
                'label' => 'Тип',
            ])
            ->add('isFull', 'boolean', [
                'label' => 'Заполнено',
            ]);
        $listMapper
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    public function preUpdate($object)
    {
        // сохранить копию формы в новом формате
        $this->getConverter()->createOrUpdateClientFormResponse($object);
    }

    public function postPersist($object)
    {
        // сохранить копию формы в новом формате
        $this->getConverter()->createOrUpdateClientFormResponse($object);
        $this->getEntityManager()->flush();
    }

    public function preRemove($object)
    {
        // удалить копию формы в новом формате
        $this->getConverter()->deleteClientFormResponse($object);
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
}
