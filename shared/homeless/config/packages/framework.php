<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    $container->parameters()
        ->set('env(TRUSTED_HOSTS)', 'localhost')
        ->set('env(TRUSTED_PROXIES)', '127.0.0.1')
    ;

    $framework
        ->secret(env('APP_SECRET'))
        ->httpMethodOverride(true)
        ->trustedHosts(env('TRUSTED_HOSTS'))
        ->trustedProxies(env('TRUSTED_PROXIES'))
        ->handleAllThrowables(true)
    ;

    $framework->session()
        ->cookieLifetime(605080)
        ->gcMaxlifetime(252000)
        ->savePath(param('kernel.project_dir').'/var/sessions/'.param('kernel.environment'))
        ->cookieSecure('auto')
        ->cookieSamesite(Cookie::SAMESITE_LAX)
    ;

    $framework->phpErrors()
        ->log(true)
    ;
};
