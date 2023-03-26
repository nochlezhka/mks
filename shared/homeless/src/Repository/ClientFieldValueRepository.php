<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Repository;

use App\Entity\Client;
use App\Entity\ClientFieldValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientFieldValue|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ClientFieldValue|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ClientFieldValue> findAll()
 * @method array<ClientFieldValue> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientFieldValueRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ClientFieldValue::class);
    }

    /**
     * @return array<ClientFieldValue>
     */
    public function findByClient(Client $client): array
    {
        $queryBuilder = $this->createQueryBuilder('v')
            ->leftJoin('v.field', 'f')
            ->where('v.client = :client')
        ;

        $queryBuilder = $client->isHomeless()
            ? $queryBuilder->andWhere('f.enabled = true or f.enabledForHomeless = true')
            : $queryBuilder->andWhere('f.enabled = true');

        return $queryBuilder
            ->orderBy('f.sort', 'asc')
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByClientAndFieldCode(Client $client, string $fieldCode): ?ClientFieldValue
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.field', 'f')
            ->where('v.client = :client')
            ->andWhere('f.code = :fieldCode')
            ->setParameters([
                'client' => $client,
                'fieldCode' => $fieldCode,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
