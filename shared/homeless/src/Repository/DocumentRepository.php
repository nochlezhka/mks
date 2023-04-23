<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<Document> findAll()
 * @method array<Document> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Document::class);
    }

    public function getRegistrationDocumentsQueryBuilderByClient(Client $client): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('d');
        $queryBuilder
            ->where('d.client =  :client')
            ->orderBy('d.createdAt', 'DESC')
            ->innerJoin('d.type', 'd_t', Join::WITH, 'd_t.type IN (1,3)')
            ->setParameter('client', $client)
        ;

        return $queryBuilder;
    }
}
