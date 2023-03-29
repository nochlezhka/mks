<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App;

use App\Security\User\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile', name: 'profile')]
#[IsGranted(Role::SONATA_ADMIN)]
class Profile extends AbstractController
{
    public function __invoke(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->forward('sonata.admin.controller.crud::editAction', [], [
            '_sonata_admin' => 'sonata.user.admin.user',
            'id' => $user->getId(),
        ]);
    }
}
