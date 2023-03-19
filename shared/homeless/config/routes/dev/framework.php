<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@FrameworkBundle/Resources/config/routing/errors.xml')
        ->prefix('/_error')
    ;
};
