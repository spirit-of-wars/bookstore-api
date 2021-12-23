<?php

namespace App\Controller;

use App\Service\Authentication\AuthenticationAppleIdServiceAuthentication;
use App\Service\Authentication\AuthenticationFbServiceAuthentication;
use App\Service\Authentication\AuthenticationOAuth2Service;
use App\Service\Authentication\AuthenticationVkServiceAuthentication;
use App\Util\Authorization\AuthConstant;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Exception;

/**
 * @Route("/auth", name="auth_")
 */
class AuthController extends BaseController
{
    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'UserManager';
    }

    /**
     * @Route("/by-mail", name="authenticate_by_mail", methods={"POST"})
     * @param Request $request
     * @param AuthenticationOAuth2Service $authenticationOAuth2Service
     * @return JsonResponse
     * @throws LoaderError
     * @throws NonUniqueResultException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function authenticateByMail(Request $request, AuthenticationOAuth2Service $authenticationOAuth2Service)
    {
        $email = $request->get(AuthConstant::USER_AUTH_FIELD_EMAIL);

        $tokensPare = $authenticationOAuth2Service->tryPrepareTokensPare($email);
        if ($tokensPare) {

            return $this->prepareResponse($tokensPare);
        }

        if (!$authenticationOAuth2Service->sendConfirmLink($email)) {

            return $this->prepareErrorResponse('Error while authentication');
        }

        return $this->prepareResponse('ok');
    }

    /**
     * @Route("/authentication", name="confirmByLink", methods={"GET"})
     * @param Request $request
     * @param AuthenticationOAuth2Service $authenticationOAuth2Service
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function confirmByLink(Request $request, AuthenticationOAuth2Service $authenticationOAuth2Service)
    {
        $token = $request->get(AuthConstant::USER_AUTH_FIELD_CONFIRM_TOKEN);

        if (is_null($token)) {
            return $this->prepareErrorResponse('token required');
        }

        $arrToken = $authenticationOAuth2Service->confirmByLink($token);

        if (is_null($arrToken)) {
            return $this->prepareErrorResponse('failed confirmed by link');
        }

        return $this->prepareResponse($arrToken);
    }

    /**
     * @Route("/refresh", name="refresh_access_token", methods={"POST"})
     * @param Request $request
     * @param AuthenticationOAuth2Service $authenticationOAuth2Service
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function refreshAccessToken(Request $request, AuthenticationOAuth2Service $authenticationOAuth2Service)
    {
        $refreshToken = $request->get(AuthConstant::USER_AUTH_FIELD_REFRESH_TOKEN);
        $accessToken = $request->get(AuthConstant::USER_AUTH_FIELD_ACCESS_TOKEN);

        $newTokens = $authenticationOAuth2Service->refreshTokens($refreshToken, $accessToken);

        if (empty($newTokens)) {
            return $this->prepareErrorResponse('Token is outdated or does not exist');
        }

        return $this->prepareResponse($newTokens);
    }

    /**
     * @Route("/by-vk", name="authentication_by_vk", methods={"GET"})
     * @param Request $request
     * @param AuthenticationVkServiceAuthentication $authenticationVkService
     * @return JsonResponse
     * @throws Exception
     */
    public function authenticationByVk(
        Request $request,
        AuthenticationVkServiceAuthentication $authenticationVkService
    )
    {

        $arrAuthentication = $authenticationVkService->generateTokensForUser(
            [
                'code' => $request->get(AuthConstant::USER_AUTH_FIELD_CODE),
                'redirectUri' => $request->get(AuthConstant::USER_AUTH_FIELD_REDIRECT_URI)
            ]
        );

        if (is_null($arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'message' => 'error authentication with vk'
                ]
            );
        }

        if (array_key_exists('hashMailUsr', $arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'hashMailUsr' => $arrAuthentication['hashMailUsr'],
                    'message' => 'error authentication by vkontakte'
                ]
            );
        }

        return $this->prepareResponse($arrAuthentication);
    }

    /**
     * @Route("/by-facebook", name="authentication_by_facebook", methods={"GET"})
     * @param Request $request
     * @param AuthenticationFbServiceAuthentication $authenticationFacebookService
     * @return JsonResponse
     * @throws Exception
     */
    public function authenticationByFacebook(
        Request $request,
        AuthenticationFbServiceAuthentication $authenticationFacebookService
    )
    {
        $arrAuthentication = $authenticationFacebookService->generateTokensForUser(
            [
                'code' => $request->get(AuthConstant::USER_AUTH_FIELD_CODE),
                'redirectUri' => $request->get(AuthConstant::USER_AUTH_FIELD_REDIRECT_URI)
            ]
        );

        if (is_null($arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'message' => 'error authentication with facebook'
                ]
            );
        }

        if (array_key_exists('hashMailUsr', $arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'hashMailUsr' => $arrAuthentication['hashMailUsr'],
                    'message' => 'error authentication by facebook'
                ]
            );
        }

        return $this->prepareResponse($arrAuthentication);
    }

    /**
     * @Route("/by-apple-id", name="authentication_by_apple_id", methods={"GET"})
     * @param Request $request
     * @param AuthenticationAppleIdServiceAuthentication $authenticationAppleIdServiceAuthentication
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function authenticationByAppleId(
        Request $request,
        AuthenticationAppleIdServiceAuthentication $authenticationAppleIdServiceAuthentication
    )
    {
        $arrAuthentication = $authenticationAppleIdServiceAuthentication->generateTokensForUser(
            [
                'code' => $request->get(AuthConstant::USER_AUTH_FIELD_CODE),
            ]
        );

        if (is_null($arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'message' => 'error authentication with apple id'
                ]
            );
        }

        if (is_array($arrAuthentication) && array_key_exists('hashMailUsr', $arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'hashMailUsr' => $arrAuthentication['hashMailUsr'],
                    'message' => 'error authentication by apple id'
                ]
            );
        }

        return $this->prepareResponse($arrAuthentication);
    }

    /**
     * @Route("/mobile/by-vk", name="authentication_mobile_by_vk", methods={"POST"})
     * @param Request $request
     * @param AuthenticationVkServiceAuthentication $authenticationVkServiceAuthentication
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function authMobileVk(
        Request $request,
        AuthenticationVkServiceAuthentication $authenticationVkServiceAuthentication
    )
    {
        $arrAuthentication = $authenticationVkServiceAuthentication->checkUserData(
            $request->get('token')
        );

        if (array_key_exists('hashMailUsr', $arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'hashMailUsr' => $arrAuthentication['hashMailUsr'],
                    'message' => 'error authentication by vkontakte'
                ]
            );
        }

        return $this->prepareResponse($arrAuthentication);
    }

    /**
     * @Route("/mobile/by-fb", name="authentication_mobile_by_fb", methods={"POST"})
     * @param Request $request
     * @param AuthenticationFbServiceAuthentication $authenticationVkServiceAuthentication
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function authMobileFb(
        Request $request,
        AuthenticationFbServiceAuthentication $authenticationVkServiceAuthentication
    )
    {
        $arrAuthentication = $authenticationVkServiceAuthentication->checkUserData(
            $request->get('token')
        );

        if (array_key_exists('hashMailUsr', $arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'hashMailUsr' => $arrAuthentication['hashMailUsr'],
                    'message' => 'error authentication by fb'
                ]
            );
        }

        return $this->prepareResponse($arrAuthentication);
    }

    /**
     * @Route("/mobile/by-apple-id", name="authentication_mobile_by_apple_id", methods={"POST"})
     * @param Request $request
     * @param AuthenticationAppleIdServiceAuthentication $authenticationAppleIdServiceAuthentication
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function authMobileAppleId(
        Request $request,
        AuthenticationAppleIdServiceAuthentication $authenticationAppleIdServiceAuthentication
    )
    {
        $arrAuthentication = $authenticationAppleIdServiceAuthentication->checkUserData(
            $request->get('token')
        );

        if (array_key_exists('hashMailUsr', $arrAuthentication)) {
            return $this->prepareErrorResponse(
                [
                    'hashMailUsr' => $arrAuthentication['hashMailUsr'],
                    'message' => 'error authentication by apple id'
                ]
            );
        }

        return $this->prepareResponse($arrAuthentication);
    }

    /**
     * @Route("/by-code", name="authentication_by_code", methods={"POST"})
     * @param Request $request
     * @param AuthenticationOAuth2Service $authenticationOAuth2Service
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function authenticationByCode(
        Request $request,
        AuthenticationOAuth2Service $authenticationOAuth2Service
    )
    {
        $code = $request->get(AuthConstant::USER_AUTH_FIELD_CONFIRM_CODE);

        if (is_null($code)) {
            return $this->prepareErrorResponse('code required');
        }

        $arrToken = $authenticationOAuth2Service->confirmByCode($code);

        if (is_null($arrToken)) {
            return $this->prepareErrorResponse('failed confirmed by code');
        }

        return $this->prepareResponse($arrToken);
    }
}
