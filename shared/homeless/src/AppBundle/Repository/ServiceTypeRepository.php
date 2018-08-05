<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceType;
use Doctrine\ORM\EntityRepository;

class ServiceTypeRepository extends EntityRepository
{
    /**
     * Получение доступных типов для сертификата
     *
     * @param Service $service
     * @return ServiceType[]
     */
    public function getAvailableForService(Service $service)
    {

        $qb = $this->createQueryBuilder('t');
        $qb->orderBy('t.sort', 'ASC');
        $result = $qb->getQuery()->execute();

        return null === $result ? [] : $result;
    }
}
