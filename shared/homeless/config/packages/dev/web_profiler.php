<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebProfilerConfig;

return static function (
    WebProfilerConfig $webProfiler,
    FrameworkConfig $framework,
): void {
    $webProfiler->toolbar(true);

    $framework->profiler()
        ->collectSerializerData(true)
    ;
};
