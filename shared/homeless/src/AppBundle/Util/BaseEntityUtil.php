<?php


namespace AppBundle\Util;


use AppBundle\Entity\BaseEntity;

class BaseEntityUtil
{
    /**
     * Сортирует объекты-наследники `BaseEntity` по полю `sort` по возрастанию.
     *
     * Масив `$array` меняется in-place
     *
     * @param BaseEntity[] $array
     */
    public static function sortEntities(array &$array)
    {
        usort(
            $array,
            function (BaseEntity $a, BaseEntity $b) {
                return $a->getSort() - $b->getSort();
            });
    }
}