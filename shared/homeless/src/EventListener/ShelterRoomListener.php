<?php

namespace App\EventListener;

use App\Entity\ShelterHistory;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: ShelterHistory::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: ShelterHistory::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: ShelterHistory::class)]
class ShelterRoomListener
{
    /**
     * При создании договора номер устанавливается равным id
     */
    public function postPersist(ShelterHistory $shelterHistory, LifecycleEventArgs $args): void
    {
        $room = $shelterHistory->getRoom();
        if (!empty($room)) {
            $currentOccupants = $room->getCurrentOccupants();
            $em = $args->getObjectManager();
            $room->setCurrentOccupants($currentOccupants + 1);
            $em->persist($room);
            $em->flush();
        }
    }

    public function postRemove(ShelterHistory $shelterHistory, LifecycleEventArgs $args): void
    {
        $room = $shelterHistory->getRoom();
        if (!empty($room)) {
            $currentOccupants = $room->getCurrentOccupants();
            $em = $args->getObjectManager();
            $room->setCurrentOccupants(($currentOccupants == 0 ) ? 0 : $currentOccupants - 1);
            $em->persist($room);
            $em->flush();
        }
    }

    public function preUpdate(ShelterHistory $shelterHistory, PreUpdateEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $unitOfWork = $em->getUnitOfWork();
        $updatedEntities = $unitOfWork->getEntityChangeSet($shelterHistory);
        if ($args->hasChangedField('room')) {
            $changeSetId = $updatedEntities['room'][0]->getId();
            $oldRoom = $em->getRepository('AppBundle:ShelterRoom')->find($changeSetId);
            $oldRoom->setCurrentOccupants(($oldRoom->getCurrentOccupants() == 0) ? 0 : $oldRoom->getCurrentOccupants() - 1);
            $em->persist($oldRoom);
            $newRoom = $shelterHistory->getRoom();
            $newRoom->setCurrentOccupants($newRoom->getCurrentOccupants() + 1);
            $em->persist($newRoom);
        }
        $em->flush();
    }
}
