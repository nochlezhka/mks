<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Admin\ClientAdmin;
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
        ->call('setTemplate', ['layout', 'admin/layout.html.twig'])
        ->call('setTemplate', ['edit', 'admin/client/edit.html.twig'])
    ;

    $services->set('sonata.user.admin.user', UserAdmin::class);
};
