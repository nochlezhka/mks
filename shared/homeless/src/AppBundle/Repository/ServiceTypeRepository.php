<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceType;
use Doctrine\ORM\EntityRepository;

class ServiceTypeRepository extends EntityRepository
{
    /**
     * Получение доступных типов услуг
     *
     * @return ServiceType[]
     */
    public function getAvailable()
    {

        $qb = $this->createQueryBuilder('t');

        // Запрещаем создание услуги типов: одежда, гигиена, костыли.
        // Функционал переезжает в сущность DeliveryItem.
        $qb
            ->where('t.id NOT IN (3, 17, 22)')
            ->orderBy('t.sort', 'ASC');
        $result = $qb->getQuery()->execute();

        return null === $result ? [] : $result;
    }
}
