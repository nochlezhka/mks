<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $webpackEncore, FrameworkConfig $framework): void {
    $webpackEncore
        ->outputPath(param('kernel.project_dir').'/public/_assets/js')
        ->scriptAttributes('defer', true)
        ->preload(true)
    ;

    $framework->assets()
        ->jsonManifestPath(param('kernel.project_dir').'/public/_assets/js/manifest.json')
    ;
};
