<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

use App\Entity\User;
use Symfony\Config\SonataUserConfig;

return static function (SonataUserConfig $sonataUser): void {
    $sonataUser
        ->securityAcl(false)
        ->managerType('orm')
    ;

    $sonataUser->class()
        ->user(User::class)
    ;

    $sonataUser->impersonating()
        ->route('sonata_admin_dashboard')
    ;

    $resetting = $sonataUser->resetting();
    $resetting->email()
        ->address('sonata@localhost')
        ->senderName('Sonata Admin')
    ;
};
