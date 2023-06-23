<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Contract::class)]
class ContractListener
{
    /**
     * При создании договора номер устанавливается равным id
     */
    public function postPersist(Contract $contract, LifecycleEventArgs $event): void
    {
        if (!empty($contract->getNumber())) {
            return;
        }

        $contract->setNumber((string) $contract->getId());

        $em = $event->getObjectManager();
        $em->persist($contract);
        $em->flush($contract);
    }
}
