<?php

namespace App\Util\Authorization;

use App\Controller\BaseController;
use App\Interfaces\Auth\AuthorizationNeighborInterface;
use App\Interfaces\Auth\AuthorizationProcessorInterface;
use App\Interfaces\Auth\AuthorizationRbacInterface;

/**
 * Class AuthorizationProcessorFactory
 * @package App\Util\Authentication
 */
class AuthorizationProcessorFactory
{
    /**
     * @param BaseController $controller
     * @return AuthorizationProcessorInterface|null
     */
    public static function createByController(BaseController $controller) : ?AuthorizationProcessorInterface
    {
        if ($controller instanceof AuthorizationRbacInterface) {
            return new AuthorizationRbacProcessor();
        }

        if ($controller instanceof AuthorizationNeighborInterface) {
            return new AuthorizationNeighborProcessor();
        }

        return null;
    }
}
