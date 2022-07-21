<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\ShelterHistory;
use Buzz\Message\Request;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ShelterRoomListener
{
    /**
     * При создании договора номер устанавливается равным id
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof ShelterHistory && !empty($entity->getRoom())) {
            $room = $entity->getRoom();
            $roomId = $room->getId();
            $currentOccupants = $room->getCurrentOccupants();

            $em = $args->getEntityManager();

            $room->setCurrentOccupants($currentOccupants + 1);

            $em->persist($room);
            $em->flush();
        }

    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();


        if ($entity instanceof ShelterHistory && !empty($entity->getRoom())) {
            $room = $entity->getRoom();
            $roomId = $room->getId();
            $currentOccupants = $room->getCurrentOccupants();

            $em = $args->getEntityManager();

            $room->setCurrentOccupants(($currentOccupants == 0 ) ? 0 : $currentOccupants - 1);

            $em->persist($room);
            $em->flush();
        }

    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();
        $updatedEntities = $unitOfWork->getEntityChangeSet($entity);

        if ($entity instanceof ShelterHistory && $args->hasChangedField('room')) {

            $changeSetId = $updatedEntities['room'][0]->getId();

            $oldRoom = $em->getRepository('AppBundle:ShelterRoom')->find($changeSetId);
            $oldRoom->setCurrentOccupants(($oldRoom->getCurrentOccupants() == 0) ? 0 : $oldRoom->getCurrentOccupants() - 1);

            $em->persist($oldRoom);


            $newRoom = $entity->getRoom();
            $newRoom->setCurrentOccupants($newRoom->getCurrentOccupants() + 1);

            $em->persist($newRoom);

            // $shelterHistory = $em->getRepository('AppBundle:ShelterHistory')->find($entity->getId());
            // $shelterHistory->setComment($entity->getComment());
            // $shelterHistory->setDiphtheriaVaccinationDate($entity->getDiphtheriaVaccinationDate());
            // $shelterHistory->setFluorographyDate($entity->getFluorographyDate());
            // $shelterHistory->setHepatitisVaccinationDate($entity->getHepatitisVaccinationDate());
            // $shelterHistory->setTyphusVaccinationDate($entity->getTyphusVaccinationDate());
            // $shelterHistory->setDateFrom($entity->getDateFrom());
            // $shelterHistory->setDateTo($entity->getDateTo());
            // $shelterHistory->setRoom($entity->getRoom());
            // $shelterHistory->setClient($entity->getClient());
            // $shelterHistory->setStatus($entity->getStatus());
            // $shelterHistory->setContract($entity->getContract());

            // $em->persist($shelterHistory);
        }

        $em->flush();

    }
}
