<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\Client;

use App\Admin\ClientAdmin;
use App\Entity\ContractStatus;
use App\Repository\ContractStatusRepository;
use App\Security\User\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Мои клиенты
 */
#[Route('/my-clients', name: 'my_clients')]
#[IsGranted(Role::SONATA_ADMIN)]
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
            '_sonata_admin' => ClientAdmin::class,
            'filter' => $filter,
        ]);
    }
}
