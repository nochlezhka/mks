<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\KnpPaginatorConfig;

return static function (KnpPaginatorConfig $knpPaginator): void {
    $knpPaginator->defaultOptions();
    $knpPaginator->template();
};
