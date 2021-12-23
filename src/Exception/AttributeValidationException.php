<?php

namespace App\Exception;

use App\Enum\ExceptionCodeEnum;

/**
 * Class MethodCallException
 * @package App\Exception
 */
class AttributeValidationException extends MifException
{
    /**
     * MethodCallException constructor.
     * @param string $message
     */
    public function __construct($message = "")
    {
        parent::__construct($message, ExceptionCodeEnum::ATTRIBUTE_VALIDATION_ERROR_CODE);
    }
}
