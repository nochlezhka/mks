<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ClientFormField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientFormField|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ClientFormField|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ClientFormField> findAll()
 * @method array<ClientFormField> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientFormFieldRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ClientFormField::class);
    }
}
