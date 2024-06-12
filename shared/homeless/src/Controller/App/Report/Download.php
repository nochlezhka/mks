<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Controller\App\Report;

use App\Service\ReportService;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @throws Exception
 * @throws \Doctrine\DBAL\Exception
 * @throws \Doctrine\DBAL\Driver\Exception
 */
#[Route('/report-download', name: 'reportDownload')]
final class Download extends AbstractController
{
    public function __invoke(ReportService $reportService, Request $request): Response
    {
        $type = $request->get('type');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$type.'.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->sendHeaders();

        $reportService->generate(
            $type,
            $request->get('dateFrom'),
            $request->get('dateTo'),
            $request->get('userId'),

            $request->get('createClientdateFrom'),
            $request->get('createClientFromTo'),
            $request->get('createServicedateFrom'),
            $request->get('createServiceFromTo'),

            $request->get('homelessReason'),
            $request->get('disease'),
            $request->get('breadwinner'),
        );

        return $response;
    }
}
