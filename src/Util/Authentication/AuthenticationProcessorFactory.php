<?php

namespace App\Util\Authentication;

use App\Controller\BaseController;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthenticationProcessorInterface;

/**
 * Class AuthenticationProcessorFactory
 * @package App\Util\Authentication
 */
class AuthenticationProcessorFactory
{
    /**
     * @param BaseController $controller
     * @return AuthenticationProcessorInterface|null
     */
    public static function createByController($controller) : ?AuthenticationProcessorInterface
    {
        if ($controller instanceof AuthenticationOAuth2Interface) {
            return new AuthenticationOAuth2Processor();
        }

        return null;
    }
}
