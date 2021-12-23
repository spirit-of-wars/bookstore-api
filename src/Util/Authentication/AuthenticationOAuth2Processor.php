<?php

namespace App\Util\Authentication;

use App\Constants;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthenticationProcessorInterface;
use App\Util\Authorization\AuthConstant;
use App\Util\Common\ErrorsCollectorTrait;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Mif;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthenticationOAuth2Processor
 * @package App\Util\Authentication
 *
 *
 */
class AuthenticationOAuth2Processor implements AuthenticationProcessorInterface
{
    use ErrorsCollectorTrait;

    /**
     * @param Request $request
     * @param AuthenticationOAuth2Interface $controller
     * @param string $action
     * @throws NonUniqueResultException
     */
    public function runAuthentication(Request $request, $controller, string $action) : void
    {
        $commonUserService = Mif::getServiceProvider()->UserService;
        $userManager = Mif::getUserManager();
        if (!$controller->checkActionNeedAuthentication($action)) {
            $guestUser = $commonUserService->getByEmail(Constants::GUEST_EMAIL);
            $userManager->setUser($guestUser);
            return;
        }

        $tokenRequest = $request->headers->get(AuthConstant::USER_AUTH_HEADER_ACCESS_TOKEN);
        if (!$tokenRequest) {
            $this->addError('Токен доступа отсутствует');
            return;
        }

        $token = Mif::getServiceProvider()->AccessTokenService->getByActualToken($tokenRequest);
        if (is_null($token)) {
            $this->addError('Токен доступа неверный');
            return;
        }

        $user = $commonUserService->getByToken($token->getToken());
        if (is_null($user)) {
            $this->addError('Токен доступа неверный');
            //TODO сделать запись в лог через логгер. Ошибка, что явно что-то фундаментальное сломалось
            //  "There is access token without user! Access token id={$token->getId()}"
            return;
        }

        $userManager->setUser($user);
    }

    /**
     * @return JsonResponse
     */
    public function getErrorResponse() : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errorCode' => Response::HTTP_UNAUTHORIZED,
            'errorDetails' => $this->getErrors(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
