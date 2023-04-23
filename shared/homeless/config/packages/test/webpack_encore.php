<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $webpackEncore): void {
    $webpackEncore
        ->strictMode(false)
    ;
};
