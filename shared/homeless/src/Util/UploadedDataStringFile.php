<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedDataStringFile extends UploadedFile
{
    public function __construct(string $dataString, string $originalName)
    {
        preg_match('/data:([^;]*);base64,(.*)/', $dataString, $matches);
        $mimeType = $matches[1];

        $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
        $data = base64_decode($matches[2], true);
        file_put_contents($filePath, $data);
        $error = null;

        parent::__construct($filePath, $originalName, $mimeType, $error, true);
    }
}
