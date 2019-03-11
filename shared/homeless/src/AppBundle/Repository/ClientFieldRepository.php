<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ClientFieldRepository extends EntityRepository
{
    public function findByEnabledAll()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.enabled = true or f.enabledForHomeless = true')
            ->orderBy('f.sort', 'asc')
            ->getQuery()
            ->getResult();
    }
}
