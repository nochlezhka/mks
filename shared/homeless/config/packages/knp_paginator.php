<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\KnpPaginatorConfig;

return static function (KnpPaginatorConfig $knpPaginator): void {
    $knpPaginator->defaultOptions();
    $knpPaginator->template();
};
