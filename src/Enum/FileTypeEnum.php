<?php

namespace App\Enum;

use App\Enum\Core\Enum;

class FileTypeEnum extends Enum
{
    const JPEG = 'image/jpeg';
    const PNG = 'image/png';
    const GIF = 'image/gif';
    const SVG = 'image/svg+xml';
    const PDF = 'application/pdf';

    /**
     * @param $fileType
     * @return bool
     */
    public static function validateFileType($fileType)
    {
        return in_array($fileType, self::getList());
    }
}
