<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Util;

use App\Entity\BaseEntity;

class BaseEntityUtil
{
    /**
     * Сортирует объекты-наследники `BaseEntity` по полю `sort` по возрастанию.
     *
     * Массив `$array` меняется in-place
     *
     * @param array<BaseEntity> $array
     */
    public static function sortEntities(array &$array): void
    {
        usort($array, static fn (BaseEntity $a, BaseEntity $b): int => $a->getSort() - $b->getSort());
    }
}
