<?php


namespace App\Admin;


use App\Entity\ClientForm;
use App\Entity\ClientFormResponse;
use App\Service\MetaService;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Дочерний класс `ClientFormResponseAdmin` для работы с анкетой проживающего.
 * ID формы анкеты захардкожен в `$this->formId`
 *
 * @package App\Admin
 */
#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'resident_form_response',
    'model_class' => ClientFormResponse::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]
class ResidentFormResponseAdmin extends ClientFormResponseAdmin
{
    protected $formId = ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID;

    // переопределения для роутов и breadcrumbs
    protected $baseRouteName = 'resident_form_response';
    protected $baseRoutePattern = 'resident_form_response';
    protected $classnameLabel = 'resident_form_response';

    public function __construct(MetaService $metaService)
    {
        parent::__construct();
        $this->metaService = $metaService;
    }

    /**
     * @inheritDoc
     * @param ClientFormResponse $object
     */
    public function preValidate($object): void
    {
        if ($this->metaService->isClientFormsEnabled()) {
            parent::preValidate($object);
            return;
        }
        // если анкеты в новом формате ещё не открыли, запрещаем редактирование синхронных копий,
        // составленных из анкет в старом формате
        if ($object->getResidentQuestionnaireId() !== null) {
            throw new AccessDeniedException("Изменение копии анкеты запрещено.");
        }
        parent::preValidate($object);
    }
}
