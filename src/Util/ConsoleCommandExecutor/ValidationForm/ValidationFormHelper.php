<?php

namespace App\Util\ConsoleCommandExecutor\ValidationForm;

use App\Mif;

/**
 * Class ValidationFormHelper
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class ValidationFormHelper
{
    /**
     * @return string
     */
    public static function getMapFilePath()
    {
        return self::getUtilCompiledPath() . '/' . self::getMapFileName();
    }

    /**
     * @return string
     */
    public static function getMapFileName()
    {
        return 'map.json';
    }

    /**
     * @return string
     */
    public static function getUtilPath()
    {
        return Mif::getProjectDir() . '/util/ValidationForm';
    }

    /**
     * @return string
     */
    public static function getUtilCompiledPath()
    {
        return Mif::getProjectDir() . '/util/ValidationFormCompiled';
    }

    /**
     * @return string
     */
    public static function getControllerPath()
    {
        return Mif::getProjectDir() . '/src/Controller';
    }
}
