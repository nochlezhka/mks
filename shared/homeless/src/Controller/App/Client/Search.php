<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\Client;

use App\Security\User\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client/list', name: 'client_search')]
#[IsGranted(Role::SONATA_ADMIN)]
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
