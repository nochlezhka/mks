<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig): void {
    $twig
        ->debug(param('kernel.debug'))
        ->strictVariables(param('kernel.debug'))
        ->formThemes([
            'form/fields.html.twig',
            '@FOSCKEditor/Form/ckeditor_widget.html.twig',
            '@SonataForm/Form/datepicker.html.twig',
            '@SonataFormatter/Form/formatter.html.twig',
            '@VichUploader/Form/fields.html.twig',
            '@VichUploader/Form/fields.html.twig',
        ])
        ->global('org_name_short', env('ORG_NAME_SHORT'))
        ->global('org_name', env('ORG_NAME'))
        ->global('org_description', env('ORG_DESCRIPTION'))
        ->global('org_description_short', env('ORG_DESCRIPTION_SHORT'))
        ->global('org_contacts_full', env('ORG_CONTACTS_FULL'))
        ->global('org_city', env('ORG_CITY'))
        ->global('dispensary_name', env('DISPENSARY_NAME'))
        ->global('dispensary_address', env('DISPENSARY_ADDRESS'))
        ->global('dispensary_phone', env('DISPENSARY_PHONE'))
        ->global('employment_name', env('EMPLOYMENT_NAME'))
        ->global('employment_address', env('EMPLOYMENT_ADDRESS'))
        ->global('employment_inspection', env('EMPLOYMENT_INSPECTION'))
        ->global('sanitation_name', env('SANITATION_NAME'))
        ->global('sanitation_address', env('SANITATION_ADDRESS'))
        ->global('sanitation_time', env('SANITATION_TIME'))
        ->global('logo_path', env('LOGO_PATH'))
    ;
};
