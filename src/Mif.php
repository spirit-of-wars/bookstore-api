<?php

namespace App;

use App\MifTools\EntityPersistHolder\EntityPersistHolder;
use App\MifTools\MifServiceProvider;
use App\MifTools\UserManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class Mif
 * @package App
 */
class Mif
{
    /** @var Kernel */
    public static $app;

    /** @var MifServiceProvider */
    private static $serviceProvider;

    /** @var EntityPersistHolder */
    private static $persistHolder;

    /** @var UserManager */
    private static $userManager;

    /**
     * @return Kernel
     */
    public static function defineApplication()
    {
        $kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
        self::$app = $kernel;
        return $kernel;
    }

    /**
     * @return MifServiceProvider
     */
    public static function getServiceProvider()
    {
        return self::$serviceProvider;
    }

    /**
     * @return EntityPersistHolder
     */
    public static function getPersistHolder()
    {
        if (!isset(self::$persistHolder)) {
            self::$persistHolder = new EntityPersistHolder();
        }

        return self::$persistHolder;
    }

    /**
     * @return UserManager
     */
    public static function getUserManager()
    {
        if (!isset(self::$userManager)) {
            self::$userManager = new UserManager();
        }

       return self::$userManager;
    }

    /**
     * @return string
     */
    public static function getProjectDir()
    {
        return self::$app->getProjectDir();
    }

    /**
     * @return ManagerRegistry
     */
    public static function getDoctrine()
    {
        /** @var ManagerRegistry $doctrine */
        $doctrine = self::$app->getContainer()->get('doctrine');
        return $doctrine;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function getEnvConfig($name)
    {
        return $_ENV[$name] ?? null;
    }

    /**
     * @param MifServiceProvider $serviceProvider
     */
    public static function setServiceProvider($serviceProvider)
    {
        self::$serviceProvider = $serviceProvider;
    }
}
