<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\ShelterRoom;

use App\Entity\ShelterRoom;
use App\Security\User\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/shelterroom/{id}/edit', name: 'shelter_room_edit')]
#[IsGranted(Role::ADMIN)]
class Edit extends AbstractController
{
    public function __invoke(ShelterRoom $room): Response
    {
        $form = $this->createFormBuilder()
            ->setAction('/app/shelterroom/save')
            ->setMethod('GET')
            ->add('number', null, [
                'label' => 'Номер комнаты',
            ])
            ->add('maxOccupants', null, [
                'label' => 'Максимальное кол-во жильцов',
                'required' => false,
            ])
            ->add('currentOccupants', null, [
                'label' => 'Текущее кол-во жильцов',
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->getForm()
        ;

        $formData = [
            'id' => $room->getId(),
            'number' => $room->getNumber(),
            'maxOccupants' => $room->getMaxOccupants(),
            'currentOccupants' => $room->getCurrentOccupants(),
            'comment' => $room->getComment(),
        ];

        $form->setData($formData);

        return $this->render('admin/shelter_room_form.html.twig', [
            'room' => ['id' => $formData['id']],
            'form' => $form->createView(),
            'form_title' => 'Редактирование комнаты',
            'form_id' => 'saveRoom',
            'form_action' => 'edit',
        ]);
    }
}
