<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Repository;

use App\Entity\ContractStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContractStatus|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ContractStatus|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ContractStatus> findAll()
 * @method array<ContractStatus> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractStatusRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ContractStatus::class);
    }
}
