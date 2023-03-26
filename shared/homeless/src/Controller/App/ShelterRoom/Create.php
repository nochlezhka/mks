<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\ShelterRoom;

use App\Security\User\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/shelterroom/create', name: 'shelter_room_add')]
#[IsGranted(Role::ADMIN)]
class Create extends AbstractController
{
    public function __invoke(Request $request): Response
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

        $form->handleRequest($request);

        return $this->render('admin/shelter_room_form.html.twig', [
            'room' => [],
            'form' => $form->createView(),
            'form_title' => 'Добавление комнаты',
            'form_id' => 'saveRoom',
            'form_action' => 'save',
        ]);
    }
}
