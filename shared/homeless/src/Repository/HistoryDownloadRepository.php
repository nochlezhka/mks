<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\HistoryDownload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HistoryDownload|null   find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryDownload|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<HistoryDownload> findAll()
 * @method array<HistoryDownload> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryDownloadRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, HistoryDownload::class);
    }
}
