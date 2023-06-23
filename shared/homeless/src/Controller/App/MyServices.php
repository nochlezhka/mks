<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller\App;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Оказанные мной услуги
 */
#[Route('/my-services', name: 'my_services')]
class MyServices extends AbstractController
{
    public function __invoke(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->forward('sonata.admin.controller.crud::listAction', [], [
            '_sonata_admin' => 'app.service.admin',
            'filter' => ['createdBy' => ['value' => $user->getId()]],
        ]);
    }
}
