<?php


namespace AppBundle\Util;


class ClientFormUtil
{
    /**
     * Преобразует многострочный текст, разделённый переводами строки, в массив строк.
     * Все строки проходят через `trim`, и из массива удаляются пустые строки.
     *
     * Функция вернёт массив всегда, даже если на входе `null`.
     *
     * @param string $optionsText
     * @return string[]
     */
    public static function optionsTextToArray($optionsText)
    {
        if ($optionsText === null) {
            return [];
        }
        $list = preg_split("/[\r\n]+/", $optionsText);
        if ($list === false) {
            return [];
        }
        $list = array_filter(array_map('trim', $list), function ($v) { return $v != ''; });
        return $list;
    }

    /**
     * Преобразует массив строк в многострочный текст, где строки разделяются переводами строк.
     * Все строки проходят через `trim`, пустые строки удаляются.
     *
     * @param string[] $array
     * @return string
     */
    public static function arrayToOptionsText($array)
    {
        $array = array_filter(array_map('trim', $array), function ($v) { return $v != ''; });
        return implode("\n", $array);
    }
}
