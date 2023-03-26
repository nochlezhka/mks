<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Service;

class DOCXNamingService
{
    /**
     * Получение названия файла при скачивании
     */
    public function createName(DownloadableInterface $downloadable, string $format): string
    {
        return $downloadable->getNamePrefix().'-'.$downloadable->getId().'.'.$format;
    }
}
