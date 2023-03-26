<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

use App\Admin;
use App\Security\User\Role;
use Symfony\Config\SonataAdminConfig;

return static function (SonataAdminConfig $sonataAdmin): void {
    $sonataAdmin
        ->title(' ')
        ->titleLogo('/render/logo.png')
        ->showMosaicButton(false)
    ;

    $sonataAdmin->options()
        ->defaultAdminRoute('edit')
        ->useIcheck(false)
    ;

    $sonataAdmin->security()
        ->handler('sonata.admin.security.handler.role')
    ;
    $sonataAdmin->breadcrumbs()
        ->childAdminRoute('show')
    ;
    $sonataAdmin->assets()
        ->javascripts([
            'bundles/sonataadmin/app.js',
            'bundles/sonataform/app.js',
            'bundles/fosckeditor/ckeditor.js',
        ])
        ->stylesheets([
            'bundles/sonataadmin/app.css',
            'bundles/sonataform/app.css',
        ])
    ;
    $sonataAdmin->templates()
        ->layout('admin/layout.html.twig')
        ->tabMenuTemplate('admin/tab_menu_template.html.twig')
        ->select('admin/list__select.html.twig')
    ;

    $dashboard = $sonataAdmin->dashboard();

    $clientsDashboard = $dashboard->group('app.clients')
        ->label('Клиенты')
        ->icon('<i class="fa fa-users"></i>')
    ;
    $clientsDashboard->item()
        ->route('my_clients')
        ->label('Мои клиенты')
        ->roles([
            Role::APP_CLIENT_ADMIN_LIST,
            Role::ADMIN,
        ])
    ;
    $clientsDashboard->item()
        ->route('add_client')
        ->label('Добавить клиента')
        ->roles([
            Role::APP_CLIENT_ADMIN_CREATE,
            Role::ADMIN,
        ])
    ;
    $clientsDashboard->item()
        ->route('my_ex_clients')
        ->label('Мои бывшие клиенты')
        ->roles([
            Role::APP_CLIENT_ADMIN_LIST,
            Role::ADMIN,
        ])
    ;

    $profileDashboard = $dashboard->group('app.profile')
        ->label('Личный кабинет')
        ->icon('<i class="fa fa-user"></i>')
    ;
    $profileDashboard->item()
        ->route('my_services')
        ->label('Оказанные мной услуги')
        ->roles([
            Role::APP_SERVICE_ADMIN_ALL,
            Role::ADMIN,
        ])
    ;
    $profileDashboard->item()
        ->route('profile')
        ->label('Мой профиль')
        ->roles([
            Role::SONATA_USER_ADMIN_USER_EDIT,
            Role::ADMIN,
        ])
    ;

    $settingsDashboard = $dashboard->group('app.settings')
        ->label('Настройки')
        ->icon('<i class="fa fa-wrench"></i>')
    ;
    $settingsDashboard->item()->admin(Admin\ClientFieldAdmin::class);
    $settingsDashboard->item()->admin(Admin\RegionAdmin::class);
    $settingsDashboard->item()->admin(Admin\ServiceTypeAdmin::class);
    $settingsDashboard->item()->admin(Admin\ContractItemTypeAdmin::class);
    $settingsDashboard->item()->admin(Admin\GeneratedDocumentTypeAdmin::class);
    $settingsDashboard->item()->admin(Admin\GeneratedDocumentStartTextAdmin::class);
    $settingsDashboard->item()->admin(Admin\GeneratedDocumentEndTextAdmin::class);
    $settingsDashboard->item()->admin(Admin\CertificateTypeAdmin::class);
    $settingsDashboard->item()->admin(Admin\MenuItemAdmin::class);
    $settingsDashboard->item()->admin(Admin\PositionAdmin::class);
    $settingsDashboard->item()->admin(Admin\UserAdmin::class);
    $settingsDashboard->item()->admin(Admin\ClientFormAdmin::class);
    $settingsDashboard->item()->admin(Admin\ShelterRoomAdmin::class);
    $settingsDashboard->roles([Role::ADMIN]);
};
