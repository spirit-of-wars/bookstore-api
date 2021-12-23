<?php

namespace App\Validation\ValidationFormMacros;

/**
 * Class AbstractMacros
 * @package App\Validation\ValidationFormMacros
 */
abstract class AbstractMacros
{
    /**
     * @param string $param
     * @return array|null
     */
    abstract function run($param);
}
