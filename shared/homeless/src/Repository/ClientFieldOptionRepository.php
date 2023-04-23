<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ClientFieldOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientFieldOption|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ClientFieldOption|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ClientFieldOption> findAll()
 * @method array<ClientFieldOption> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientFieldOptionRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ClientFieldOption::class);
    }
}
