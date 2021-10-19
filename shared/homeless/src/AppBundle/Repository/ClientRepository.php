<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;


class ClientRepository extends EntityRepository
{
    const MAX_SEARCH_RESULTS = 30;

    public function search($searchStr)
    {
        $searchVals = $this->prepareSearchStr($searchStr);
        if (count($searchVals) === 0) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(['c.id', 'c.firstname', 'c.middlename', 'c.lastname', 'c.birthDate'])
            ->from('AppBundle:Client', 'c');

        foreach ($searchVals as $i => $v) {
            $parName = ":val$i";
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('c.firstname', $parName),
                    $qb->expr()->like('c.middlename', $parName),
                    $qb->expr()->like('c.lastname', $parName)
                ))
            ->setParameter($parName, $v.'%');
        }

        $qb->setMaxResults(self::MAX_SEARCH_RESULTS);
        return $qb->getQuery()->getResult();
    }

    protected function prepareSearchStr($searchStr)
    {
        $vals = array_map(function($v) {
            return strtolower(trim($v));
        },explode(' ', $searchStr, 3));

        return array_filter($vals, function($v) {
            return strlen($v) > 2;
        });
    }

}
