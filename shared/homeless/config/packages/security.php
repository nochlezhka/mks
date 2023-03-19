<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Entity\User;
use App\Security\User\Role;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $userProvider = $security->provider('users');
    $userProvider->entity()
        ->class(User::class)
        ->property('usernameCanonical')
    ;

    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false)
    ;

    $mainFirewall = $security->firewall('main')
        ->pattern('.*')
        ->context('user')
    ;
    $mainFirewall->formLogin()
        ->provider('users')
        ->defaultTargetPath('my_clients')
    ;
    $mainFirewall->logout()
        ->target('/login')
    ;
    $mainFirewall->switchUser();

    $security->accessControl()
        ->path('^/login$')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS])
    ;
    $security->accessControl()
        ->path('^/resetting')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS])
    ;
    $security->accessControl()
        ->path('^/.*')
        ->roles([Role::ADMIN, Role::SONATA_ADMIN])
    ;

    $security->roleHierarchy(Role::ADMIN, Role::USER);
    $security->roleHierarchy(Role::SUPER_ADMIN, [
        Role::USER,
        Role::SONATA_ADMIN,
        Role::ADMIN,
        Role::ALLOWED_TO_SWITCH,
        Role::SONATA,
    ]);
    $security->roleHierarchy(Role::SONATA, [
        Role::SONATA_PAGE_ADMIN_PAGE_EDIT,
        Role::SONATA_PAGE_ADMIN_BLOCK_EDIT,
    ]);

    $security->passwordHasher(LegacyPasswordHasherInterface::class)
        ->algorithm('sha512')
    ;
    $security->passwordHasher(UserInterface::class)
        ->algorithm('auto')
        ->migrateFrom(LegacyPasswordHasherInterface::class)
    ;
};
