<?php

namespace App\Interfaces\Auth;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use App\Interfaces\ErrorResponseProviderInterface;

/**
 * Interface AuthorizationProcessorInterface
 * @package App\Util\Authentication
 */
interface AuthorizationProcessorInterface extends ErrorResponseProviderInterface
{
    /**
     * @param Request $request
     * @param BaseController $controller
     * @param string $action
     */
    public function runAuthorization(Request $request, $controller, string $action);

    /**
     * @return bool
     */
    public function hasErrors() : bool;

    /**
     * @return array
     */
    public function getErrors() : array;
}
