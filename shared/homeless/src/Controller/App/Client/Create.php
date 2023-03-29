<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\Client;

use App\Security\User\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/add-client', name: 'add_client')]
#[IsGranted(Role::SONATA_ADMIN)]
class Create extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->forward('sonata.admin.controller.crud::createAction', [], [
            '_sonata_admin' => 'app.client.admin',
        ]);
    }
}
