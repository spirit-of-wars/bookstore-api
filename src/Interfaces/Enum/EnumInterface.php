<?php

namespace App\Interfaces\Enum;

/**
 * Interface EnumInterface
 * @package App\Interfaces\Enum
 */
interface EnumInterface
{
    /**
     * @return array
     */
    public static function getList();

    /**
     * @param mixed $value
     * @return bool
     */
    public static function validateValue($value);
}
