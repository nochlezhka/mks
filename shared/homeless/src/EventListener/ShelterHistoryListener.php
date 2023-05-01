<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\ShelterHistory;
use App\Entity\ShelterRoom;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: ShelterHistory::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: ShelterHistory::class)]
#[AsDoctrineListener(event: Events::onFlush)]
class ShelterHistoryListener
{
    /**
     * При создании договора номер устанавливается равным id
     */
    public function postPersist(ShelterHistory $shelterHistory, LifecycleEventArgs $args): void
    {
        $room = $shelterHistory->getRoom();
        if (empty($room)) {
            return;
        }

        $room->setCurrentOccupants($room->getCurrentOccupants() + 1);

        $em = $args->getObjectManager();
        $em->persist($room);
        $em->flush();
    }

    public function postRemove(ShelterHistory $shelterHistory, LifecycleEventArgs $args): void
    {
        $room = $shelterHistory->getRoom();
        if (empty($room)) {
            return;
        }

        $room->setCurrentOccupants(max($room->getCurrentOccupants() - 1, 0));

        $em = $args->getObjectManager();
        $em->persist($room);
        $em->flush();
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $shelterRoomRepository = $em->getRepository(ShelterRoom::class);
        $unitOfWork = $em->getUnitOfWork();
        $updatedEntities = $unitOfWork->getScheduledEntityUpdates();

        foreach ($updatedEntities as $entity) {
            if (!$entity instanceof ShelterHistory) {
                continue;
            }

            $changeSet = $unitOfWork->getEntityChangeSet($entity);
            if (!isset($changeSet['room'])) {
                continue;
            }

            $oldRoom = $shelterRoomRepository->find($changeSet['room'][0]->getId());
            $oldRoom->setCurrentOccupants(max($oldRoom->getCurrentOccupants() - 1, 0));

            $newRoom = $shelterRoomRepository->find($changeSet['room'][1]->getId());
            $newRoom->setCurrentOccupants($newRoom->getCurrentOccupants() + 1);

            $em->persist($oldRoom);
            $unitOfWork->recomputeSingleEntityChangeSet($em->getClassMetadata(ShelterRoom::class), $oldRoom);

            $em->persist($newRoom);
            $unitOfWork->recomputeSingleEntityChangeSet($em->getClassMetadata(ShelterRoom::class), $newRoom);
        }
    }
}
