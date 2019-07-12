<?php

namespace AppBundle\Repository;

use AppBundle\Entity\MenuItem;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class MenuItemRepository extends EntityRepository
{
    /**
     * Получение раздела по его коду
     *
     * @param string $code
     * @return MenuItem|null
     */
    public function findByCode($code)
    {
        try {
            return $this->createQueryBuilder('mi')
                ->andWhere('mi.code = :code')
                ->setParameter('code', $code)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
