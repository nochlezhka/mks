<?php

namespace App\Service;

class DOCXNamingService
{
    /**
     * Получение названия файла при скачивании
     *
     * @param DownloadableInterface $downloadable
     * @param string $format
     * @return string
     */
    public function createName(DownloadableInterface $downloadable, $format)
    {
        return $downloadable->getNamePrefix() . '-' . $downloadable->getId() . '.' . $format;
    }
}
