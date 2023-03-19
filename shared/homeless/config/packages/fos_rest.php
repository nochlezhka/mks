<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FosRestConfig;

return static function (FosRestConfig $fosRest): void {
    $fosRest->paramFetcherListener();

    $view = $fosRest->view()
        ->format('json', true)
        ->format('xml', true)
    ;
    $view->viewResponseListener();

    $formatListener = $fosRest->formatListener();
    $formatListener->rule([
        'path' => '^/api',
        'priorities' => ['json'],
        'fallback_format' => 'json',
        'prefer_extension' => true,
    ]);
    $formatListener->rule([
        'path' => '^/',
        'stop' => true,
    ]);

    $fosRest->serializer()
        ->serializeNull(true)
    ;
};
