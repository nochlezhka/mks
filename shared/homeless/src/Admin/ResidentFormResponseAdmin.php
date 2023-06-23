<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ClientForm;
use App\Entity\ClientFormResponse;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

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
    protected $classnameLabel = 'resident_form_response';

    public function preValidate(object $object): void
    {
        if (!$object instanceof ClientFormResponse) {
            return;
        }

        parent::preValidate($object);
    }

    protected function generateBaseRouteName(bool $isChildAdmin = false): string
    {
        return 'resident_form_response';
    }

    protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'resident_form_response';
    }
}
