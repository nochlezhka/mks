<?php

namespace App\Form\Type;

use App\Entity\Client;
use App\Entity\ClientFieldValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class AppFileType extends AbstractType
{
    private $storage;

    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    private function getDownloadUri(FormInterface $form, array $options)
    {
        if (empty($form->getParent())) {
            return null;
        }

        if (empty($client = $form->getParent()->getData())) {
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

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['download_uri'] = $this->getDownloadUri($form, $options);
        $view->vars['filename'] = basename($view->vars['download_uri']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_file';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }


    public function getParent()
    {
        return FileType::class;
    }
}
