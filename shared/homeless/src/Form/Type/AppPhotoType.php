<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Vich\UploaderBundle\Form\Type\VichImageType;

#[AutoconfigureTag(name: 'form.type', attributes: ['alias' => 'app_photo'])]
final class AppPhotoType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void {}

    public function getBlockPrefix(): string
    {
        return 'app_photo';
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getParent(): string
    {
        return VichImageType::class;
    }
}
