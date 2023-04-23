<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monolog): void {
    $mainHandler = $monolog->handler('main');
    $mainHandler
        ->type('fingers_crossed')
        ->actionLevel(LogLevel::ERROR)
        ->handler('nested')
    ;
    $mainHandler->excludedHttpCode()
        ->code(Response::HTTP_FORBIDDEN)
        ->code(Response::HTTP_METHOD_NOT_ALLOWED)
    ;

    $monolog->handler('nested')
        ->type('error_log')
        ->level(LogLevel::DEBUG)
    ;
};
