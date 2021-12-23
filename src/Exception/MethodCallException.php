<?php

namespace App\Exception;

use App\Enum\ExceptionCodeEnum;

/**
 * Class MethodCallException
 * @package App\Exception
 */
class MethodCallException extends MifException
{
    /**
     * MethodCallException constructor.
     * @param string $message
     */
    public function __construct($message = "")
    {
        parent::__construct($message, ExceptionCodeEnum::METHOD_CALL_ERROR_CODE);
    }
}
