<?php

namespace App\Helper;

/**
 * Class NamespaceHelper
 * @package App\Helper
 */
class NamespaceHelper
{
    /**
     * @param string $name
     * @param string|array $defaultNamespace
     * @return string
     */
    public static function defineClassName($name, $defaultNamespace)
    {
        if (class_exists($name) || interface_exists($name) || trait_exists($name)) {

            return $name;
        }

        if (is_string($defaultNamespace)) {

            return $defaultNamespace . '\\' . $name;
        }

        if (is_array($defaultNamespace)) {
            foreach ($defaultNamespace as $value) {
                $fullName = $value . '\\' . $name;
                if (class_exists($fullName) || interface_exists($fullName) || trait_exists($fullName)) {

                    return $fullName;
                }
            }
        }

        return $name;
    }

    /**
     * @return string
     */
    public static function getEntityDefaultNamespace()
    {
        return 'App\Entity';
    }

    /**
     * @return string
     */
    public static function getBehaviorDefaultNamespace()
    {
        return 'App\EntitySupport\Behavior';
    }

    /**
     * @return string
     */
    public static function getEnumDefaultNamespace()
    {
        return 'App\Enum';
    }

    /**
     * @return string
     */
    public static function getInterfaceDefaultNamespace()
    {
        return 'App\Interfaces';
    }

    /**
     * @return string
     */
    public static function getEntityInterfaceDefaultNamespace()
    {
        return 'App\EntitySupport\Interfaces';
    }

    /**
     * @return string
     */
    public static function getValidationFormMacrosDefaultNamespace()
    {
        return 'App\Validation\ValidationFormMacros';
    }
}
