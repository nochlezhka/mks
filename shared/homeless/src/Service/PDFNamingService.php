<?php

namespace App\Service;

class PDFNamingService implements FileNamingServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createName(DownloadableInterface $downloadable): string
    {
        return $downloadable->getNamePrefix() . '-' . $downloadable->getId() . '.pdf';
    }
}
