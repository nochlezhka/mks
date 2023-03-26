<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Admin;

use App\Entity\ClientForm;
use App\Entity\ClientFormResponse;
use App\Service\MetaService;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Дочерний класс `ClientFormResponseAdmin` для работы с анкетой проживающего.
 * ID формы анкеты захардкожен в `$this->formId`
 */
#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.resident_form_response.admin',
    'label' => 'resident_form_response',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => ClientFormResponse::class,
])]
class ResidentFormResponseAdmin extends ClientFormResponseAdmin
{
    protected ?int $formId = ClientForm::RESIDENT_QUESTIONNAIRE_FORM_ID;

    // переопределения для роутов и breadcrumbs
    protected $baseRouteName = 'resident_form_response';
    protected $baseRoutePattern = 'resident_form_response';
    protected $classnameLabel = 'resident_form_response';

    public function __construct(MetaService $metaService)
    {
        parent::__construct();
        $this->metaService = $metaService;
    }

    public function preValidate(object $object): void
    {
        if (!$object instanceof ClientFormResponse) {
            return;
        }

        // если анкеты в новом формате ещё не открыли, запрещаем редактирование синхронных копий,
        // составленных из анкет в старом формате
        if (!$this->metaService->isClientFormsEnabled() && $object->getResidentQuestionnaireId() !== null) {
            throw new AccessDeniedException('Изменение копии анкеты запрещено.');
        }

        parent::preValidate($object);
    }
}
