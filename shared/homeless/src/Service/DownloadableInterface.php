<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Service;

interface DownloadableInterface
{
    /**
     * Возвращает префикс названия файла
     */
    public function getNamePrefix(): string;

    /**
     * Возвращает идентификатор сущности
     */
    public function getId(): ?int;
}
