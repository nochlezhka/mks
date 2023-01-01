<?php


namespace App\Admin;


use App\Entity\ClientForm;
use App\Entity\ClientFormResponse;
use App\Service\MetaService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Дочерний класс `ClientFormResponseAdmin` для работы с анкетой проживающего.
 * ID формы анкеты захардкожен в `$this->formId`
 *
 * @package App\Admin
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
    public function preValidate($object): void
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
}
