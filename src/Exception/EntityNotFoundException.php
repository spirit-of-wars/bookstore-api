<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class EntityNotFoundException
 * @package App\Exception
 */
class EntityNotFoundException extends MifException
{
    /**
     * MethodCallException constructor.
     */
    public function __construct($message)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
    }
}
