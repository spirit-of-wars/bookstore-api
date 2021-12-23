<?php

namespace App\Util\Authorization;

use App\Interfaces\Auth\AuthorizationProcessorInterface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Util\Common\ErrorsCollectorTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Mif;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthorizationRbacProcessor
 * @package App\Util\Authentication
 */
class AuthorizationRbacProcessor implements AuthorizationProcessorInterface
{
    use ErrorsCollectorTrait;

    /**
     * @param Request $request
     * @param AuthorizationRbacInterface $controller
     * @param string $action
     * @return void
     */
    public function runAuthorization(Request $request, $controller, string $action) : void
    {
        $allowedRolesForAction = $controller->getRightForAction($action);

        if (empty($allowedRolesForAction)) {
            return;
        }

        $user = Mif::getUserManager()->getUser();

        $userRoles = $user->getRoles();

        if (empty($userRoles)) {
            $this->addError('У Вас не хватает прав для этого ресурса');
            return;
        }

        $hasAccess = false;
        foreach ($userRoles as $userRole) {
            if (in_array($userRole->getName(), $allowedRolesForAction)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $this->addError('У Вас не хватает прав для этого ресурса');
        }

        return;
    }

    /**
     * @return JsonResponse
     */
    public function getErrorResponse() : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errorCode' => Response::HTTP_FORBIDDEN,
            'errorDetails' => $this->getErrors(),
        ], Response::HTTP_FORBIDDEN);
    }
}
