<?php

namespace App\Service;

interface DownloadableInterface
{
    /**
     * Возвращает префикс названия файла
     *
     * @return string
     */
    public function getNamePrefix();

    /**
     * Возвращает идентификатор сущности
     *
     * @return mixed
     */
    public function getId();
}
