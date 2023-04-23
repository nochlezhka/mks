<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml')
        ->prefix('/_wdt')
    ;

    $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml')
        ->prefix('/_profiler')
    ;
};
