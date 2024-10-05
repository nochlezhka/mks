<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Util;

final class ClientFormUtil
{
    /**
     * Преобразует многострочный текст, разделённый переводами строки, в массив строк.
     * Все строки проходят через `trim`, и из массива удаляются пустые строки.
     *
     * Функция вернёт массив всегда, даже если на входе `null`.
     *
     * @return array<string>
     */
    public static function optionsTextToArray(?string $optionsText): array
    {
        if ($optionsText === null) {
            return [];
        }

        $list = preg_split("/[\r\n]+/", $optionsText);
        if ($list === false) {
            return [];
        }

        return array_filter(array_map('trim', $list), static fn ($v): bool => $v !== '');
    }

    /**
     * Преобразует массив строк в многострочный текст, где строки разделяются переводами строк.
     * Все строки проходят через `trim`, пустые строки удаляются.
     */
    public static function arrayToOptionsText(array $array): string
    {
        return implode("\n", array_filter(array_map('trim', $array), static fn ($v): bool => $v !== ''));
    }
}
