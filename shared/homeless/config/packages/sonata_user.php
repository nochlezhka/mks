<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Entity\User;
use Symfony\Config\SonataUserConfig;

return static function (SonataUserConfig $sonataUser): void {
    $sonataUser->class()
        ->user(User::class)
    ;

    $sonataUser->impersonating()
        ->route('sonata_admin_dashboard')
    ;

    $resetting = $sonataUser->resetting();
    $resetting->email()
        ->address('%env(SONATA_RESETTING_ADDRESS)%')
        ->senderName('%env(SONATA_RESETTING_SENDER)%')
    ;
};
