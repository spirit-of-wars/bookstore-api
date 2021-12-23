<?php

namespace App\Interfaces\Auth;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use App\Interfaces\ErrorResponseProviderInterface;

/**
 * Interface AuthenticationProcessorInterface
 * @package App\Interfaces\Auth
 */
interface AuthenticationProcessorInterface extends ErrorResponseProviderInterface
{
    /**
     * @param Request $request
     * @param BaseController $controller
     * @param string $action
     */
    public function runAuthentication(Request $request, $controller, string $action);

    /**
     * @return bool
     */
    public function hasErrors() : bool;

    /**
     * @return array
     */
    public function getErrors() : array;
}
