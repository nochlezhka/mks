<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Log\LogLevel;
use Symfony\Config\MonologConfig;

return static function (MonologConfig $config): void {
    $mainHandler = $config->handler('main');
    $mainHandler
        ->type('stream')
        ->path(param('kernel.logs_dir').'/'.param('kernel.environment').'.log')
        ->actionLevel(LogLevel::DEBUG)
    ;
    $mainHandler->channels()
        ->elements(['!event'])
    ;

    $consoleHandler = $config->handler('console');
    $consoleHandler
        ->type('console')
        ->channels(['elements' => ['!event', '!console', '!doctrine']])
    ;
    $consoleHandler->processPsr3Messages()
        ->enabled(true)
    ;
};
