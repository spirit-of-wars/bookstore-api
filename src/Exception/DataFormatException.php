<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class DataFormatException
 * @package App\Exception
 */
class DataFormatException extends MifException
{
    /**
     * MethodCallException constructor.
     */
    public function __construct()
    {
        parent::__construct('Неверный формат данных', Response::HTTP_BAD_REQUEST);
    }
}
