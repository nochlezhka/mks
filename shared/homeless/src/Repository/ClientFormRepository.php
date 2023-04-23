<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ClientForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientForm|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ClientForm|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ClientForm> findAll()
 * @method array<ClientForm> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientFormRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ClientForm::class);
    }
}
