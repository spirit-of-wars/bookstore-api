<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator;

/**
 * Class Helper
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator
 */
class EntityGeneratorHelper
{
    /**
     * @param string $entityName
     * @return string
     */
    public static function getEntitySimpleName($entityName)
    {
        $arr = explode('\\', $entityName);
        return array_pop($arr);
    }

    /**
     * @param string $entityName
     * @return string
     */
    public static function getEntityNamespace($entityName)
    {
        $arr = explode('\\', $entityName);
        if (count($arr) == 1) {
            return '';
        }

        array_pop($arr);
        return implode('\\', $arr);
    }

    /**
     * @param string $entityName
     * @return string
     */
    public static function getEntityPath($entityName)
    {
        $entityName = str_replace('\\', '/', $entityName);
        return self::pathToEntities() . $entityName . '.php';
    }

    /**
     * @param string $entityName
     * @return string
     */
    public static function getEntityRepositoryPath($entityName)
    {
        $entityName = str_replace('\\', '/', $entityName);
        return self::pathToRepositories() . $entityName . 'Repository.php';
    }

    /**
     * @return string
     */
    public static function pathToEntities()
    {
        return '/src/Entity/';
    }

    /**
     * @return string
     */
    public static function pathToRepositories()
    {
        return '/src/Repository/';
    }

    /**
     * @param string $entityName
     * @return string
     */
    public static function getYamlEntityPath($entityName)
    {
        $entityName = str_replace('\\', '/', $entityName);
        return self::pathToYamlEntities() . $entityName . '.yaml';
    }

    /**
     * @param string $entityName
     * @return string
     */
    public static function getDescriptionEntityPath($entityName)
    {
        $entityName = str_replace('\\', '/', $entityName);
        return '/util/EntityDescriptions/' . $entityName . '.yaml';
    }

    /**
     * @return string
     */
    public static function pathToYamlEntities()
    {
        return '/util/Entity/';
    }
}
