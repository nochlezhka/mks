<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Security\User\Role;
use Symfony\Config\SonataAdminConfig;

return static function (SonataAdminConfig $sonataAdmin): void {
    $sonataAdmin
        ->title(' ')
        ->titleLogo(env('LOGO_PATH'))
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
        ->roles([Role::EMPLOYEE])
    ;
    $clientsDashboard->item()
        ->route('my_clients')
        ->label('Мои клиенты')
    ;
    $clientsDashboard->item()
        ->route('add_client')
        ->label('Добавить клиента')
    ;
    $clientsDashboard->item()
        ->route('my_ex_clients')
        ->label('Мои бывшие клиенты')
    ;

    $profileDashboard = $dashboard->group('app.profile')
        ->label('Личный кабинет')
        ->icon('<i class="fa fa-user"></i>')
        ->roles([Role::EMPLOYEE])
    ;
    $profileDashboard->item()
        ->route('my_services')
        ->label('Оказанные мной услуги')
    ;
    $profileDashboard->item()
        ->route('profile')
        ->label('Мой профиль')
    ;

    $settingsDashboard = $dashboard->group('app.settings')
        ->label('Настройки')
        ->icon('<i class="fa fa-wrench"></i>')
        ->roles([Role::SUPER_ADMIN])
    ;
    $settingsDashboard->item()->admin('app.client_field.admin');
    $settingsDashboard->item()->admin('app.region.admin');
    $settingsDashboard->item()->admin('app.service_type.admin');
    $settingsDashboard->item()->admin('app.contract_item_type.admin');
    $settingsDashboard->item()->admin('app.generated_document_type.admin');
    $settingsDashboard->item()->admin('app.generated_document_start_text.admin');
    $settingsDashboard->item()->admin('app.generated_document_end_text.admin');
    $settingsDashboard->item()->admin('app.certificate_type.admin');
    $settingsDashboard->item()->admin('app.menu_item.admin');
    $settingsDashboard->item()->admin('app.position.admin');
    $settingsDashboard->item()->admin('sonata.user.admin.user');
    $settingsDashboard->item()->admin('app.client_form.admin');
    $settingsDashboard->item()->admin('app.shelter_room.admin');
};
