<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller\App\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/add-client', name: 'add_client')]
final class Create extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->forward('sonata.admin.controller.crud::createAction', [], [
            '_sonata_admin' => 'app.client.admin',
        ]);
    }
}
