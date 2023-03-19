<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Controller\App\Report;

use App\Repository\ClientFieldRepository;
use App\Repository\UserRepository;
use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report', name: 'report')]
class Report extends AbstractController
{
    public function __invoke(
        ClientFieldRepository $clientFieldRepository,
        UserRepository $userRepository,
        ReportService $reportService,
    ): Response {
        return $this->render('admin/report.html.twig', [
            'users' => $userRepository->findBy([
                'enabled' => true,
            ]),
            'types' => $reportService->getTypes(),
            'optionsHomelessReason' => $clientFieldRepository->findOneBy(['code' => 'homelessReason'])?->getOptionsArray() ?? [],
            'optionsDisease' => $clientFieldRepository->findOneBy(['code' => 'disease'])?->getOptionsArray() ?? [],
            'optionsBreadwinner' => $clientFieldRepository->findOneBy(['code' => 'breadwinner'])?->getOptionsArray() ?? [],
        ]);
    }
}
