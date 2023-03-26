<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller;

use App\Repository\ShelterRoomRepository;
use App\Security\User\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ShelterRoomController extends \Sonata\AdminBundle\Controller\CRUDController
{
    public function __construct(
        private readonly ShelterRoomRepository $shelterRoomRepository,
    ) {}

    #[IsGranted(Role::ADMIN)]
    public function listAction(Request $request): Response
    {
        $roomData = [];
        foreach ($this->shelterRoomRepository->findAll() as $item) {
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
