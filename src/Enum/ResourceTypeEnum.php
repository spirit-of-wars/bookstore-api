<?php

namespace App\Enum;

use App\Enum\Core\Enum;
use App\Interfaces\Enum\ReferenceInterface;

/**
 * Class ProductSourceTypeEnum
 * @package App\Enum
 */
class ResourceTypeEnum extends Enum implements ReferenceInterface
{
    const IMAGE = 'image';
    const AUDIO = 'audio';
    const VIDEO = 'video';
    const DOCUMENT = 'document';
    const URI = 'uri';

    /**
     * @return array
     */
    public static function getReferences()
    {
        return [
            self::IMAGE => 'Изображение',
            self::AUDIO => 'Аудио',
            self::VIDEO => 'Видео',
            self::DOCUMENT => 'Документ',
            self::URI => 'Внешняя ссылка',
        ];
    }

    /**
     * @param $mimeType
     * @return string
     */
    public static function getSourceTypeFromFile($mimeType)
    {
        switch ($mimeType) {
            case FileTypeEnum::JPEG:
            case FileTypeEnum::PNG:
            case FileTypeEnum::GIF:
            case FileTypeEnum::SVG:
                return self::IMAGE;
            case FileTypeEnum::PDF:
                return self::DOCUMENT;
        }
    }
}
