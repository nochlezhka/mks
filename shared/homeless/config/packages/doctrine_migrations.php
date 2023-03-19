<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\DoctrineMigrationsConfig;

return static function (DoctrineMigrationsConfig $doctrineMigrations): void {
    $doctrineMigrations->migrationsPath('DoctrineMigrations', param('kernel.project_dir').'/migrations');
};
