<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\VichUploaderConfig;

return static function (VichUploaderConfig $vichUploader): void {
    $vichUploader->dbDriver('orm');
    $vichUploader->metadata()
        ->type('attribute')
    ;

    $vichUploader->mappings('client_photo')
        ->uriPrefix('/uploads/images/client/photo')
        ->uploadDestination(param('kernel.project_dir').'/public/uploads/images/client/photo')
        ->namer('vich_uploader.namer_hash')
        ->directoryNamer('vich_uploader.directory_namer_subdir')
    ;
    $vichUploader->mappings('client_field_file')
        ->uriPrefix('/uploads/files/client/field')
        ->uploadDestination(param('kernel.project_dir').'/public/uploads/files/client/field')
        ->namer('vich_uploader.namer_hash')
        ->directoryNamer('vich_uploader.directory_namer_subdir')
    ;
    $vichUploader->mappings('document_file')
        ->uriPrefix('/uploads/files/document')
        ->uploadDestination(param('kernel.project_dir').'/public/uploads/files/document')
        ->namer('vich_uploader.namer_hash')
        ->directoryNamer('vich_uploader.directory_namer_subdir')
    ;
};
