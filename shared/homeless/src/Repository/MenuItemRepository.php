<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Repository;

use App\Entity\MenuItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenuItem|null   find($id, $lockMode = null, $lockVersion = null)
 * @method MenuItem|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<MenuItem> findAll()
 * @method array<MenuItem> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuItemRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, MenuItem::class);
    }

    /**
     * Активность раздела по его коду
     */
    public function isEnableCode(string $code): bool
    {
        $menuItem = $this->findOneBy(['code' => $code]);

        return $menuItem?->isEnabled() ?? false;
    }
}
