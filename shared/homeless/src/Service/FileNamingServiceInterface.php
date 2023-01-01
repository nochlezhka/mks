<?php

namespace App\Service;

interface FileNamingServiceInterface
{
    /**
     * Получение названия файла при скачивании
     *
     */
    public function createName(DownloadableInterface $downloadable): string;
}
