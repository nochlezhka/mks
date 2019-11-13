<?php


namespace AppBundle\Admin;


use AppBundle\Entity\ClientForm;

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
}
