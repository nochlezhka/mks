<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Repository;

use App\Entity\Position;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Position|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Position|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<Position> findAll()
 * @method array<Position> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Position::class);
    }
}
