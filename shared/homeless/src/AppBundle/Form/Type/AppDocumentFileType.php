<?php

namespace AppBundle\Form\Type;

/**
 * Class AppDocumentFileType
 * @package AppBundle\Form\Type
 */
class AppDocumentFileType extends AppPhotoType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_document_file';
    }
}