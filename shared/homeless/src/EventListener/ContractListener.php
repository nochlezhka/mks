<?php

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
        if (empty($contract->getNumber())) {
            $contract->setNumber($contract->getId());
            $em = $event->getObjectManager();
            $em->persist($contract);
            $em->flush($contract);
        }
    }
}