<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('fos_ck_editor', [
        'default_config' => 'default',
        'configs' => [
            'news' => [
                // default toolbar plus Format button
                'toolbar' => [
                    ['Bold', 'Italic', 'Underline', '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo', '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'Image', 'Link', 'Unlink', 'Table'],
                    ['Format', 'Maximize', 'Source'],
                ],
                'filebrowserBrowseRoute' => 'admin_sonata_media_media_ckeditor_browser',
                'filebrowserImageBrowseRoute' => 'admin_sonata_media_media_ckeditor_browser',
                // Display images by default when clicking the image dialog browse button
                'filebrowserImageBrowseRouteParameters' => [
                    'provider' => 'sonata.media.provider.image',
                ],
                'filebrowserUploadRoute' => 'admin_sonata_media_media_ckeditor_upload',
                'filebrowserUploadRouteParameters' => [
                    'provider' => 'sonata.media.provider.file',
                ],
                // Upload file as image when sending a file from the image dialog
                'filebrowserImageUploadRoute' => 'admin_sonata_media_media_ckeditor_upload',
                'filebrowserImageUploadRouteParameters' => [
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default', // Optional, to upload in a custom context
                ],
            ],
            'homeless' => [
                // default toolbar plus Format button
                'allowedContent' => true,
                'toolbar' => [['Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', 'Source', 'TextColor']],
            ],
            'default' => [
                'toolbar' => [['Source', '-', 'Save'], '/', ['Anchor'], '/', ['Maximize']],
            ],
        ],
    ]);
};
