<?php

namespace AppBundle\Admin;

use AppBundle\Entity\ResidentQuestionnaire;
use AppBundle\Service\MetaService;
use AppBundle\Service\ResidentQuestionnaireConverter;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ResidentQuestionnaireAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'typeId',
    );

    protected $translationDomain = 'AppBundle';

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

    /**
     * При создании анкеты, в транзакции создаём её копию в новом формате.
     *
     * @param mixed $object
     * @return bool|mixed
     * @throws \Exception
     */
    public function create($object)
    {
        return $this->getEntityManager()->transactional(function(EntityManager $em) use($object) {
            $rv = parent::create($object);
            $cfr = $this->getConverter()->lockClientForm($object);
            $this->getConverter()->createOrUpdateClientFormResponse($object, $cfr);
            $em->flush();
            return $rv;
        });
    }

    /**
     * При обновлени анкеты, в транзакции создаём или обновляем её копию в новом формате.
     *
     * @param mixed $object
     * @return bool|mixed
     * @throws \Exception
     */
    public function update($object)
    {
        return $this->getEntityManager()->transactional(function(EntityManager $em) use($object) {
            $cfr = $this->getConverter()->lockClientForm($object);
            $rv = parent::update($object);
            $this->getConverter()->createOrUpdateClientFormResponse($object, $cfr);
            return $rv;
        });
    }

    /**
     * При удалении анкеты, в транзакции удалить её копию в нофом формате.
     *
     * @param mixed $object
     * @throws \Exception
     */
    public function delete($object)
    {
        $this->getEntityManager()->transactional(function($em) use($object) {
            $this->getConverter()->lockClientForm($object);
            $this->getConverter()->deleteClientFormResponse($object);
            parent::delete($object);
        });
    }

    /**
     * @inheritDoc
     * @param ResidentQuestionnaire $object
     */
    public function hasAccess($action, $object = null)
    {
        if ($this->getMetaService()->isClientFormsEnabled()
            && ($action == 'edit' || $action == 'create' || $action == 'delete' || $action == 'batchDelete')
        ) {
            return false;
        }
        return parent::hasAccess($action, $object);
    }

    /**
     * @inheritDoc
     */
    public function checkAccess($action, $object = null)
    {
        if ($this->getMetaService()->isClientFormsEnabled()
            && ($action == 'edit' || $action == 'create' || $action == 'delete' || $action == 'batchDelete')
        ) {
            throw new AccessDeniedException("Изменение анкет в старом формате запрещено.");
        }
        parent::checkAccess($action, $object);
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
