<?php

namespace App\Form\Type;

/**
 * Class AppDocumentFileType
 */
class AppDocumentFileType extends AppPhotoType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_document_file';
    }
}