<?php

namespace App\Helper;

use App\Constants;
use App\Mif;

/**
 * Class FilePathHelper
 * @package App\Helper
 */
class FilePathHelper
{
    /**
     * @return string
     */
    public static function buildFilePath()
    {
        $path = self::getPathPublicDirectory() . self::buildFileUrl();
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    /**
     * @param string $folder
     * @return string
     */
    public static function buildFileUrl($folder = 'resources')
    {
        $year = date('Y');
        $day = date('d-m');

        return Constants::DEFAULT_FILE_FOLDER . '/' . $folder . '/' . $year . '/' . $day . '/';
    }

    /**
     * @return string
     */
    public static function getPathPublicDirectory()
    {
        return Mif::getProjectDir() . Constants::PUBLIC_DIRECTORY;
    }
}
