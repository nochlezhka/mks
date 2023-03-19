<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $webpackEncore): void {
    $webpackEncore
        ->cache(true)
    ;
};
