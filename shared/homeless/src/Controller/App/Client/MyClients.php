<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller\App\Client;

use App\Entity\ContractStatus;
use App\Repository\ContractStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Мои клиенты
 */
#[Route('/my-clients', name: 'my_clients')]
class MyClients extends AbstractController
{
    public function __invoke(ContractStatusRepository $contractStatusRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $filter = ['contractCreatedBy' => ['value' => $user->getId()]];

        $inProcessStatus = $contractStatusRepository->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);
        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string) $inProcessStatus->getId()]];
        }

        return $this->forward('sonata.admin.controller.crud::listAction', [], [
            '_sonata_admin' => 'app.client.admin',
            'filter' => $filter,
        ]);
    }
}
