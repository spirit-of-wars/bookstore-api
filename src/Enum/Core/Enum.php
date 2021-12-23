<?php

namespace App\Enum\Core;

use App\Interfaces\Enum\EnumInterface;

/**
 * Class Enum
 * @package App\Enum\Core
 */
abstract class Enum implements EnumInterface
{
    /**
     * @return array
     */
    public static function getList()
    {
        try {
            $reflection = new \ReflectionClass(static::class);
            return $reflection->getConstants();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function validateValue($value)
    {
        return (array_search($value, self::getList()) !== false);
    }
}
