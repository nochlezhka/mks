<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\MopaBootstrapConfig;

return static function (MopaBootstrapConfig $mopaBootstrap): void {
    $mopaBootstrap->form();
    $mopaBootstrap->menu();
};
