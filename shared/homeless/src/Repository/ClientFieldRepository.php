<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ClientField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientField|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ClientField|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ClientField> findAll()
 * @method array<ClientField> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ClientFieldRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ClientField::class);
    }

    /**
     * @return array<ClientField>
     */
    public function findByEnabledAll(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.enabled = true or f.enabledForHomeless = true')
            ->orderBy('f.sort', 'asc')
            ->getQuery()
            ->getResult()
        ;
    }
}
