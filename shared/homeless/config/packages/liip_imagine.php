<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\LiipImagineConfig;

return static function (LiipImagineConfig $liipImagine): void {
    $liipImagine->twig()
        ->mode('lazy')
    ;

    $liipImagine->resolvers('default')
        ->webPath()
    ;

    $loaders = $liipImagine->loaders('default');
    $loaders->filesystem()
        ->dataRoot(param('kernel.project_dir').'/public/')
    ;

    $liipImagine->filterSet('cache');
    $liipImagine->filterSet('preview')
        ->quality(75)
        ->filter('thumbnail', [
            'size' => [120, 90],
            'mode' => 'outbound',
        ])
    ;
};
