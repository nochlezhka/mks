<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ShelterRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShelterRoom|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ShelterRoom|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ShelterRoom> findAll()
 * @method array<ShelterRoom> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ShelterRoomRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ShelterRoom::class);
    }
}
