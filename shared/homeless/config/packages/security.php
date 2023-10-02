<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

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
        ->path('^/((login|app/version)$|resetting)')
        ->roles(AuthenticatedVoter::PUBLIC_ACCESS)
    ;
    $security->accessControl()
        ->path('^/.*')
        ->roles(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)
    ;

    $security->roleHierarchy(Role::EMPLOYEE, [
        Role::SONATA_ADMIN,
        Role::SONATA_USER_ADMIN_USER_EDIT,

        Role::APP_CLIENT_ADMIN_ALL,
        Role::APP_DOCUMENT_ADMIN_ALL,
        Role::APP_DOCUMENT_FILE_ADMIN_ALL,
        Role::APP_CONTRACT_ADMIN_ALL,
        Role::APP_CONTRACT_ITEM_ADMIN_ALL,
        Role::APP_CERTIFICATE_ADMIN_ALL,
        Role::APP_SHELTER_HISTORY_ADMIN_ALL,
        Role::APP_RESIDENT_FORM_RESPONSE_ADMIN_ALL,
        Role::APP_GENERATED_DOCUMENT_ADMIN_ALL,
        Role::APP_NOTE_ADMIN_ALL,
        Role::APP_NOTICE_ADMIN_ALL,
        Role::APP_SERVICE_ADMIN_ALL,
    ]);
    $security->roleHierarchy(Role::SUPER_ADMIN, [
        Role::EMPLOYEE,
        Role::ALLOWED_TO_SWITCH,
    ]);

    $security->passwordHasher(LegacyPasswordHasherInterface::class)
        ->algorithm('sha512')
    ;
    $security->passwordHasher(UserInterface::class)
        ->algorithm('auto')
        ->migrateFrom(LegacyPasswordHasherInterface::class)
    ;
};
