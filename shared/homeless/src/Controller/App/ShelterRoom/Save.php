<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\ShelterRoom;

use App\Entity\ShelterRoom;
use App\Security\User\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/shelterroom/save', name: 'shelter_room_save')]
#[IsGranted(Role::ADMIN)]
class Save extends AbstractController
{
    public function __invoke(EntityManagerInterface $entityManager, Request $request): Response
    {
        $formData = $request->get('form');
        $action = $request->get('action');
        $roomId = $request->get('room_id');

        if (empty($formData['_token'])) {
            return $this->redirect('/app/shelterroom/create');
        }

        if ($action === 'edit') {
            if (empty($roomId)) {
                throw $this->createNotFoundException('No product found for id '.$roomId);
            }

            $room = $entityManager->getRepository(ShelterRoom::class)->find($roomId);
        } else {
            $room = new ShelterRoom();
        }

        $room->setNumber($formData['number']);
        $room->setMaxOccupants(empty($formData['maxOccupants']) ? null : $formData['maxOccupants']);
        $room->setCurrentOccupants(empty($formData['currentOccupants']) ? 0 : $formData['currentOccupants']);
        $room->setComment($formData['comment']);

        $entityManager->persist($room);
        $entityManager->flush();

        return $this->redirect('/app/shelterroom/list');
    }
}
