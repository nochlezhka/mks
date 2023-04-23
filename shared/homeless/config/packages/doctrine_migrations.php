<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\DoctrineMigrationsConfig;

return static function (DoctrineMigrationsConfig $doctrineMigrations): void {
    $doctrineMigrations->migrationsPath('DoctrineMigrations', param('kernel.project_dir').'/migrations');
};
