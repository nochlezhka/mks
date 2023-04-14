<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\Client;

use App\Entity\ContractStatus;
use App\Repository\ContractStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Мои бывшие клиенты
 */
#[Route('/my-ex-clients', name: 'my_ex_clients')]
class MyExClients extends AbstractController
{
    public function __invoke(ContractStatusRepository $contractStatusRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $filter = [
            'contractCreatedBy' => ['value' => $user->getId()],
            'contractStatus' => ['value' => []],
        ];

        foreach ($contractStatusRepository->findAll() as $status) {
            $statusId = $status->getId();

            if ($statusId !== ContractStatus::IN_PROCESS) {
                $filter['contractStatus']['value'][] = (string) $statusId;
            }
        }

        return $this->forward('sonata.admin.controller.crud::listAction', [], [
            '_sonata_admin' => 'app.client.admin',
            'filter' => $filter,
        ]);
    }
}
