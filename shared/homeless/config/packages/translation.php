<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->defaultLocale('ru');
    $framework->translator()
        ->defaultPath(param('kernel.project_dir').'/translations')
        ->fallbacks(['ru'])
    ;
};
