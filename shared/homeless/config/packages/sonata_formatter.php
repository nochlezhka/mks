<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\SonataFormatterConfig;

return static function (SonataFormatterConfig $sonataFormatter): void {
    $sonataFormatter->defaultFormatter('richhtml');
    $sonataFormatter->formatters('richhtml')
        ->service('sonata.formatter.text.raw')
        ->extensions([
            'sonata.formatter.twig.control_flow',
            'sonata.formatter.twig.gist',
        ])
    ;
};
