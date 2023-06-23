<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('root', '/dashboard')
        ->controller(RedirectController::class)
        ->defaults([
            'route' => 'my_clients',
            'permanent' => true,
        ])
    ;

    $routes->import('@SonataAdminBundle/Resources/config/routing/sonata_admin.xml');
    $routes->import('.', 'sonata_admin');

    $routes->import('@SonataUserBundle/Resources/config/routing/admin_security.xml');
    $routes->import('@SonataUserBundle/Resources/config/routing/admin_resetting.xml')
        ->prefix('/resetting')
    ;
};
