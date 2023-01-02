<?php

namespace App\Controller;

use App\Entity\ShelterRoom;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class ShelterRoomController extends \Sonata\AdminBundle\Controller\CRUDController
{
    private ManagerRegistry $managerRegistry;

    public function listAction(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $roomData = [];
        foreach ($this->managerRegistry->getRepository(ShelterRoom::class)->findAll() as $item) {

            $roomData[] = [
                'id' => $item->getId(),
                'number' => $item->getNumber(),
                'maxOccupants' => $item->getMaxOccupants(),
                'currentOccupants' => $item->getcurrentOccupants(),
                'comment' => $item->getComment(),
            ];
        }

        return $this->render('admin/shelter_room.html.twig', [
            'rooms' => $roomData
        ]);
    }

    #[Required]
    public function setManagerRegistry(ManagerRegistry $managerRegistry): void
    {
        $this->managerRegistry = $managerRegistry;
    }
}