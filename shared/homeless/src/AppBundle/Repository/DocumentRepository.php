<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class DocumentRepository
 * @package AppBundle\Repository
 */
class DocumentRepository extends EntityRepository
{
    /**
     * @param Client $client
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getRegistrationDocumentsQueryBuilderByClient(Client $client)
    {
        $qb =  $this->createQueryBuilder('d');
        $qb
            ->where('d.client =  :client')
            ->orderBy('d.createdAt', 'DESC')
            ->innerJoin('d.type','d_t', Join::WITH,'d_t.type = 1')
            ->setParameter('client', $client);

        return $qb;
    }
}
