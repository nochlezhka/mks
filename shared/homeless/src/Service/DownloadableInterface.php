<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

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
