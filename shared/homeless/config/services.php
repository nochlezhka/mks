<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Admin\CertificateAdmin;
use App\Admin\ClientAdmin;
use App\Admin\ClientFieldAdmin;
use App\Admin\ClientFieldOptionAdmin;
use App\Admin\ClientFormAdmin;
use App\Admin\ClientFormFieldAdmin;
use App\Admin\ContractAdmin;
use App\Admin\DistrictAdmin;
use App\Admin\DocumentAdmin;
use App\Admin\DocumentFileAdmin;
use App\Admin\GeneratedDocumentAdmin;
use App\Admin\HistoryDownloadAdmin;
use App\Admin\NoteAdmin;
use App\Admin\NoticeAdmin;
use App\Admin\RegionAdmin;
use App\Admin\ResidentFormResponseAdmin;
use App\Admin\ResidentQuestionnaireAdmin;
use App\Admin\ServiceAdmin;
use App\Admin\ShelterHistoryAdmin;
use App\Admin\UserAdmin;

return static function (ContainerConfigurator $container): void {
    $src = \dirname(__DIR__).'/src';
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\', $src.'/*')
        ->exclude($src.'/{DependencyInjection,Entity,Kernel.php}/')
    ;

    $services->set(ClientAdmin::class)
        ->call('addChild', [service(NoteAdmin::class), 'client'])
        ->call('addChild', [service(ServiceAdmin::class), 'client'])
        ->call('addChild', [service(DocumentAdmin::class), 'client'])
        ->call('addChild', [service(DocumentFileAdmin::class), 'client'])
        ->call('addChild', [service(ContractAdmin::class), 'client'])
        ->call('addChild', [service(ShelterHistoryAdmin::class), 'client'])
        ->call('addChild', [service(ResidentQuestionnaireAdmin::class), 'client'])
        ->call('addChild', [service(CertificateAdmin::class), 'client'])
        ->call('addChild', [service(GeneratedDocumentAdmin::class), 'client'])
        ->call('addChild', [service(NoticeAdmin::class), 'client'])
        ->call('addChild', [service(HistoryDownloadAdmin::class), 'client'])
        ->call('addChild', [service(ResidentFormResponseAdmin::class), 'client'])
        ->call('setTemplate', ['layout', 'admin/layout.html.twig'])
        ->call('setTemplate', ['edit', 'admin/client/edit.html.twig'])
    ;

    $services->set(ClientFieldAdmin::class)
        ->call('addChild', [service(ClientFieldOptionAdmin::class), 'field'])
    ;

    $services->set(ClientFormAdmin::class)
        ->call('addChild', [service(ClientFormFieldAdmin::class), 'form'])
    ;

    $services->set(RegionAdmin::class)
        ->call('addChild', [service(DistrictAdmin::class), 'region'])
    ;

    $services->set('sonata.user.admin.user', UserAdmin::class);
};
