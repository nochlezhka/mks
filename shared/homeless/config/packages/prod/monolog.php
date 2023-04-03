<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monolog): void {
    $mainHandler = $monolog->handler('main');
    $mainHandler
        ->type('fingers_crossed')
        ->actionLevel(LogLevel::ERROR)
        ->bufferSize(50)
        ->handler('nested')
    ;
    $mainHandler->excludedHttpCode()
        ->code(Response::HTTP_FORBIDDEN)
        ->code(Response::HTTP_METHOD_NOT_ALLOWED)
    ;

    $monolog->handler('nested')
        ->type('stream')
        ->path('php://stderr')
        ->level(LogLevel::DEBUG)
    ;

    $consoleHandler = $monolog->handler('console');
    $consoleHandler
        ->type('console')
        ->channels(['elements' => ['!event', '!doctrine']])
    ;
    $consoleHandler->processPsr3Messages()
        ->enabled(true)
    ;

    $monolog->handler('deprecation')
        ->type('stream')
        ->path('php://stderr')
        ->channels(['elements' => ['deprecation']])
    ;
    $monolog->handler('deprecation_filter')
        ->type('filter')
        ->handler('deprecation')
        ->maxLevel(LogLevel::INFO)
        ->channels(['elements' => ['php']])
    ;
};
