<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller\App;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'profile')]
final class Profile extends AbstractController
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
