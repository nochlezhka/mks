<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App;

use App\Admin\ServiceAdmin;
use App\Security\User\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Оказанные мной услуги
 */
#[Route('/my-services', name: 'my_services')]
#[IsGranted(Role::SONATA_ADMIN)]
class MyServices extends AbstractController
{
    public function __invoke(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->forward('sonata.admin.controller.crud::listAction', [], [
            '_sonata_admin' => ServiceAdmin::class,
            'filter' => ['createdBy' => ['value' => $user->getId()]],
        ]);
    }
}
