<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

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
