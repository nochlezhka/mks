<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Client;
use App\Entity\ClientFieldValue;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AutoconfigureTag(name: 'form.type', attributes: ['alias' => 'app_file'])]
class AppFileType extends AbstractType
{
    public function __construct(
        private readonly StorageInterface $storage,
    ) {}

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['download_uri'] = $this->getDownloadUri($form, $options);
        $view->vars['filename'] = basename($view->vars['download_uri']);
    }

    public function getBlockPrefix(): string
    {
        return 'app_file';
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getParent(): string
    {
        return FileType::class;
    }

    private function getDownloadUri(FormInterface $form, array $options): ?string
    {
        if (empty($form->getParent())) {
            return null;
        }

        $client = $form->getParent()->getData();
        if (empty($client)) {
            return null;
        }

        if (!$client instanceof Client) {
            return null;
        }

        if (empty($options['property_path'])) {
            return null;
        }

        $fieldCode = substr($options['property_path'], 15);

        $fieldValueObj = $client->getAdditionalFieldValueObject($fieldCode);
        if (!$fieldValueObj instanceof ClientFieldValue) {
            return null;
        }

        return $this->storage->resolveUri($fieldValueObj, 'file');
    }
}
