<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller\App\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client/list', name: 'client_search')]
class Search extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        $filter = $request->query->get('filter', []);

        if (
            (!isset($filter['search']) || empty($filter['search']['value']))
            && !isset($filter['birthDate'])
            && !isset($filter['contractCreatedBy'])
            && !isset($filter['contractStatus'])
        ) {
            $filter['search']['value'] = 'Введите запрос...';
        }

        return $this->forward('SonataAdminBundle:CRUD:list', [], [
            '_sonata_admin' => 'app.client.admin',
            'filter' => $filter,
        ]);
    }
}
