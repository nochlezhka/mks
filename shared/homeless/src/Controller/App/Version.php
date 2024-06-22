<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller\App;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/version', name: 'version')]
final class Version extends AbstractController
{
    public function __invoke(
        #[Autowire(env: 'APP_VER')]
        string $version,
    ): Response {
        return new Response($version);
    }
}
