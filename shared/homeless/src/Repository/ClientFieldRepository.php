<?php

namespace App\Repository;

use App\Entity\ClientField;
use Doctrine\ORM\EntityRepository;

class ClientFieldRepository extends EntityRepository
{
    /**
     * @return ClientField[]
     */
    public function findByEnabledAll(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.enabled = true or f.enabledForHomeless = true')
            ->orderBy('f.sort', 'asc')
            ->getQuery()
            ->getResult();
    }
}
