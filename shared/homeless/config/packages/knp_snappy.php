<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\KnpSnappyConfig;

return static function (KnpSnappyConfig $knpSnappy): void {
    $knpSnappy->pdf()
        ->binary('/var/www/symfony/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64')
    ;
    $knpSnappy->image()
        ->binary('/var/www/symfony/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64')
    ;
};
