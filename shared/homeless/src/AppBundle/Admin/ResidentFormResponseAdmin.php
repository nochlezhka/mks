<?php


namespace AppBundle\Admin;


use AppBundle\Entity\ClientForm;
use AppBundle\Entity\ClientFormField;
use AppBundle\Entity\ClientFormResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Дочерний класс `ClientFormResponseAdmin` для работы с анкетой проживавшего.
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

    /**
     * @inheritDoc
     * @param ClientFormResponse $object
     */
    public function hasAccess($action, $object = null)
    {
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
        if ($object->getResidentQuestionnaireId() !== null) {
            throw new AccessDeniedException(sprintf(
                "Изменение копии анкеты запрещено."
            ));
        }
        parent::preValidate($object);
    }
}
