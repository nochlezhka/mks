<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

#[AutoconfigureTag(name: 'form.type', attributes: ['alias' => 'app_contract_duration'])]

final class AppContractDurationType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'app_contract_duration';
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}
