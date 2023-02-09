<?php


namespace AppBundle\Admin;


use AppBundle\Entity\ClientForm;
use AppBundle\Entity\ClientFormResponse;
use AppBundle\Entity\ShelterHistory;
use AppBundle\Admin\ClientAdmin;
use AppBundle\Service\MetaService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Дочерний класс `ClientFormResponseAdmin` для работы с анкетой проживающего.
 * ID формы анкеты захардкожен в `$this->formId`
 *
 * @package AppBundle\Admin
 */
class ResidentFormResponseAdmin extends ClientFormResponseAdmin
{
    protected $formId = ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID;

    // переопределения для роутов и breadcrumbs
    protected $baseRouteName = 'resident_form_response';
    protected $baseRoutePattern = 'resident_form_response';
    protected $classnameLabel = 'resident_form_response';
    public $shelterDates = '';

    /**
     * @inheritDoc
     * @param ClientFormResponse $object
     */
    public function hasAccess($action, $object = null)
    {
        $this->shelterDates = $this->getClientShelterHistoryDates();
        if ($this->getMetaService()->isClientFormsEnabled()) {
            return parent::hasAccess($action, $object);
        }
        // если анкеты в новом формате ещё не открыли, запрещаем удаление синхронных копий,
        // составленных из анкет в старом формате

        // на всякий случай запрещаем массовое удаление
        if ($action == 'batchDelete') {
            return false;
        }
        if ($action == 'delete' && $object !== null) {
            return $object->getResidentQuestionnaireId() === null;
        }
        return parent::hasAccess($action, $object);
    }

    /**
     * @inheritDoc
     * @param ClientFormResponse $object
     */
    public function preValidate($object)
    {
        if ($this->getMetaService()->isClientFormsEnabled()) {
            parent::preValidate($object);
            return;
        }
        // если анкеты в новом формате ещё не открыли, запрещаем редактирование синхронных копий,
        // составленных из анкет в старом формате
        if ($object->getResidentQuestionnaireId() !== null) {
            throw new AccessDeniedException(sprintf(
                "Изменение копии анкеты запрещено."
            ));
        }
        parent::preValidate($object);
    }

    /**
     * @return MetaService
     */
    private function getMetaService()
    {
        return $this->getConfigurationPool()->getContainer()->get('app.meta_service');
    }

    public function getClientShelterHistoryDates()
    {
        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');
        $lived_to_formatted = '';
        $lived_from_formatted = '';

        $lived_to = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ShelterHistory')->findOneBy(['client' => $id])->getDateTo();
        $lived_from = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ShelterHistory')->findOneBy(['client' => $id])->getDateFrom();

        if (!empty($lived_to)) {
            $lived_to_formatted = $lived_to->format('d.m.Y');
        }
        if (!empty($lived_from)) {
            $lived_from_formatted = $lived_from->format('d.m.Y');
        }

        if (!empty($lived_to_formatted) && !empty($lived_from_formatted)) {
            return 'Проживание с ' . $lived_from_formatted . ' по ' . $lived_to_formatted;
        } else if (!empty($lived_to_formatted) && empty($lived_from_formatted)) {
            return 'Проживание по ' . $lived_to_formatted;
        } else if (!empty($lived_from_formatted) && empty($lived_to_formatted)) {
            return 'Проживание с ' . $lived_from_formatted;
        } else {
            return '';
        }
    }
}
