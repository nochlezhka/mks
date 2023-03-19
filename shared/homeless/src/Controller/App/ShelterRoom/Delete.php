<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\ShelterRoom;

use App\Repository\ShelterRoomRepository;
use App\Security\User\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/shelterroom/{id}/delete/', name: 'shelter_room_remove')]
#[IsGranted(Role::ADMIN)]
class Delete extends AbstractController
{
    public function __invoke(EntityManagerInterface $entityManager, ShelterRoomRepository $shelterRoomRepository, int $id): Response
    {
        $entityManager
            ->createQuery('
                DELETE FROM App\Entity\ShelterHistory AS e
                WHERE e.room = :room_id
            ')
            ->setParameter('room_id', $id)
            ->execute()
        ;

        $room = $shelterRoomRepository->find($id);

        $entityManager->remove($room);
        $entityManager->flush();

        $roomData = [];
        foreach ($shelterRoomRepository->findAll() as $item) {
            $roomData[] = [
                'id' => $item->getId(),
                'number' => $item->getNumber(),
                'maxOccupants' => $item->getMaxOccupants(),
                'currentOccupants' => $item->getcurrentOccupants(),
                'comment' => $item->getComment(),
            ];
        }

        return $this->render('admin/shelter_room.html.twig', [
            'rooms' => $roomData,
        ]);
    }
}
