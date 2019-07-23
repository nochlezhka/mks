<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class MenuItemRepository extends EntityRepository
{
    /**
     * Активность раздела по его коду
     *
     * @param string $code
     * @return bool
     */
    public function isEnableCode($code)
    {
        try {
            $menuItem = $this->createQueryBuilder('mi')
                ->andWhere('mi.code = :code')
                ->setParameter('code', $code)
                ->getQuery()
                ->getOneOrNullResult();
            if (!$menuItem) {
                return false;
            }
            return $menuItem->getEnabled();
        } catch (NonUniqueResultException $e) {
            return false;
        }
    }
}
