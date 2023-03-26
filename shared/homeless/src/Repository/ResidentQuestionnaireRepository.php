<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Repository;

use App\Entity\ResidentQuestionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResidentQuestionnaire|null   find($id, $lockMode = null, $lockVersion = null)
 * @method ResidentQuestionnaire|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<ResidentQuestionnaire> findAll()
 * @method array<ResidentQuestionnaire> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResidentQuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ResidentQuestionnaire::class);
    }
}
