<?php

namespace App\Util\ConsoleCommandExecutor\Swagger;

use App\Mif;

/**
 * Class SwaggerConstants
 * @package App\Util\ConsoleCommandExecutor\Swagger
 */
class SwaggerConstants
{
    const VERSION = '3.0.0';
    const TITLE = 'MIF API';

    const HEADER_DIR_PATH = '/var/cache/common/swagger';
    const SOURCE_DIR_PATH = '/swagger/src';
    const CONST_SOURCE_DIR_PATH = '/swagger/constSrc';

    const HEADER_FILE_NAME = 'header.json';
    const COMPONENTS_FILE_NAME = 'components.json';

    public static function getDescription()
    {
        return [
            'en' => file_get_contents(Mif::getProjectDir() . '/swagger/description/en.html'),
            'ru' => file_get_contents(Mif::getProjectDir() . '/swagger/description/ru.html'),
        ];
    }

    /**
     * @return string
     */
    public static function getHeaderPath()
    {
        return Mif::getProjectDir() . self::HEADER_DIR_PATH . '/' . self::HEADER_FILE_NAME;
    }

    /**
     * @return string
     */
    public static function getComponentsPath()
    {
        return Mif::getProjectDir() . self::CONST_SOURCE_DIR_PATH . '/' . self::COMPONENTS_FILE_NAME;
    }
}
