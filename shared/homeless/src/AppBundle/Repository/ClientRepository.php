<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;


class ClientRepository extends EntityRepository
{
    const MAX_SEARCH_RESULTS = 30;

    const MATCHES = [
      'firstname' => 'firstname',
      'lastname' => 'lastname',
      'middlename' => 'middlename',
      'birthDate' => 'birthDate',
    ];

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

        if (count($searchVals) == 3) {

            $qb->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->like('c.firstname', ':fn'),
                    $qb->expr()->like('c.middlename', ':mn'),
                    $qb->expr()->like('c.lastname', ':ln')
                ))
                ->setParameter(':fn', $searchVals[1].'%')
                ->setParameter(':mn', $searchVals[2].'%')
                ->setParameter(':ln', $searchVals[0].'%');
        }

        $qb->setMaxResults(self::MAX_SEARCH_RESULTS);
        return $qb->getQuery()->getResult();
    }

    public function searchMatches(array $search)
    {
        $search = array_filter($search,function($v){
            return !empty($v);
        });

        $search = array_map('trim',$search);
        $search = array_map('strtolower',$search);

        if ((count($search) < 3 && !isset($search['birthDate'])) || (count($search) < 2 && isset($search['birthDate']))) {
            return [];
        }

        if (!empty($search['birthDate'])) {
            $array_date = date_parse_from_format('d.m.Y', $search['birthDate']);
            $d = new \DateTime();
            $d->setDate($array_date['year'], $array_date['month'], $array_date['day']);
            $search['birthDate'] = $d->format('Y-m-d');
        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(['c.id', 'c.firstname', 'c.middlename', 'c.lastname', 'c.birthDate'])
            ->from('AppBundle:Client', 'c');

        foreach ($search as $i => $v) {

            if (in_array($i,self::MATCHES)) {
                if ($search['birthDate']) {
                    if ($i != 'birthDate') {
                        $qb->orWhere('(c.'. self::MATCHES['birthDate'] . ' = :birthDate_' . self::MATCHES[$i] .' AND c.'. self::MATCHES[$i] . ' = LOWER(:' . self::MATCHES[$i] .'))')
                            ->setParameter(':birthDate_' . self::MATCHES[$i] , $search['birthDate'])
                            ->setParameter(':' . self::MATCHES[$i], $v);
                    }
                } else {
                    $qb->andWhere('c.'. self::MATCHES[$i] . ' = LOWER(:' . self::MATCHES[$i] .')')
                        ->setParameter(':' . self::MATCHES[$i], $v);
                }

            }
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
