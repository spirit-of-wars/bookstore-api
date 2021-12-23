<?php

namespace App\Service\Authentication;

use App\Entity\Auth\AccessToken;
use App\Entity\Auth\ConfirmLink;
use App\Entity\Auth\RefreshToken;
use App\Entity\Auth\Role;
use App\Entity\Auth\SocNetworkUserData;
use App\Entity\User;
use App\Service\Entity\AccessTokenService;
use App\Service\Entity\ConfirmLinkService;
use App\Service\Entity\UserService;
use App\Service\Entity\RefreshTokenService;
use App\MifTools\EmailSender;
use App\Service\Service;
use App\Mif;
use Doctrine\ORM\NonUniqueResultException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AuthenticationOAuth2Service
 * @package App\Service\Authentication
 *
 * @property-read UserService UserService
 * @property-read AccessTokenService AccessTokenService
 * @property-read RefreshTokenService RefreshTokenService
 * @property-read ConfirmLinkService ConfirmLinkService
 * @property-read Environment EnvironmentTwig
 */
class AuthenticationOAuth2Service extends Service
{

    /**
     * @return array|string[]
     */
    public static function subscribedServicesMap() :array
    {
        return [
            'UserService' => UserService::class,
            'AccessTokenService' => AccessTokenService::class,
            'RefreshTokenService' => RefreshTokenService::class,
            'ConfirmLinkService' => ConfirmLinkService::class,
            'EnvironmentTwig' => Environment::class,
        ];
    }

    /**
     * @param $email
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function tryPrepareTokensPare($email) : ?array
    {
        $user = $this->getUserByEmail($email);
        if ($this->isTwoFactor($user)) {
            return null;
        }

        Mif::getPersistHolder()->hold();
        $currentAccessToken = $this->AccessTokenService->prepareForUser($user, false);
        $currentRefreshToken = $this->RefreshTokenService->prepareForUser($user, false);
        Mif::getPersistHolder()->commit();

        return [
            $currentAccessToken->getToken(),
            $currentRefreshToken->getToken()
        ];
    }

    /**
     * @param string $email
     * @return bool
     * @throws LoaderError
     * @throws NonUniqueResultException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendConfirmLink(string $email) : bool
    {
        Mif::getPersistHolder()->hold();

        $user = $this->getUserByEmail($email);
        $currentAccessToken = $this->AccessTokenService->prepareForUser($user, false);
        $currentRefreshToken = $this->RefreshTokenService->prepareForUser($user, false);
        $token = $this->generateConfirmToken($currentAccessToken, $currentRefreshToken);
        $code = $this->generateCode();

        $this->ConfirmLinkService->prepareForLink(
            $user,
            $currentAccessToken,
            $currentRefreshToken,
            $token,
            $code
        );

        $host = Mif::getEnvConfig('APP_HOST');
        $message = $this->renderMessage(null, "{$host}/auth/authentication?token={$token}", $code);

        $sender = new EmailSender();
        $sender->setDocRoot('/var/cache/queue');
        $result = $sender->sendMessage($email, 'Авторизация в магазине МИФ', $message);
        Mif::getPersistHolder()->commit();

        return $result;
    }

    /**
     * @param string $confirmToken
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function confirmByLink(string $confirmToken) : ?array
    {
        $confirmLinkService = $this->ConfirmLinkService;
        $currentLink = $confirmLinkService->getByToken($confirmToken);

        if (is_null($currentLink)) {
            Mif::getPersistHolder()->drop();
            Mif::getServiceProvider()->Logger->error("confirm link is not found {$currentLink}");
            return null;
        }

        $accessToken = $currentLink->getAccessToken();
        $refreshToken = $currentLink->getRefreshToken();
        $currentConfirmToken = sha1($accessToken->getToken() . '_' . $refreshToken->getToken());

        if ($confirmToken !== $currentConfirmToken) {
            Mif::getPersistHolder()->drop();
            Mif::getServiceProvider()->Logger->error(
                "confirm token is invalid current token {$currentConfirmToken} token request {$confirmToken}"
            );
            return null;
        }

        return $this->getTokensUserByCurrentLink($currentLink);
    }

    /**
     * @param int $code
     * @return null|array
     * @throws NonUniqueResultException
     */
    public function confirmByCode(int $code) : ?array
    {
        $currentLink = $this->ConfirmLinkService->getByCode($code);
        if (is_null($currentLink)) {
            return null;
        }

        return $this->getTokensUserByCurrentLink($currentLink);
    }

    /**
     * @param string $refreshTokenFromRequest
     * @param string $accessTokenFromRequest
     * @return array
     * @throws NonUniqueResultException
     */
    public function refreshTokens(string $refreshTokenFromRequest, string $accessTokenFromRequest) : array
    {
        $refreshTokenService = $this->RefreshTokenService;
        $currentRefreshToken = $refreshTokenService->getByActualToken($refreshTokenFromRequest);

        if (is_null($currentRefreshToken)) {
            return [];
        }

        Mif::getPersistHolder()->hold();
        $newRefreshToken = $refreshTokenService->refreshExpireAndActivatedDates($currentRefreshToken);
        $accessTokenService = $this->AccessTokenService;
        $currentAccessToken = $accessTokenService->getByToken($accessTokenFromRequest);
        $newAccessToken = $accessTokenService->refreshExpireAndActivatedDates(
            $currentAccessToken
        );
        Mif::getPersistHolder()->commit();

        return ['accessToken' => $newAccessToken->getToken(), 'refreshToken' => $newRefreshToken->getToken()];
    }

    /**
     * @param string $email
     * @param string $socAuthHash
     * @throws NonUniqueResultException
     */
    public function linkingAccountToSocNetwork(string $email, string $socAuthHash) : void
    {
        $userService = $this->UserService;
        $userBySocAuthHash = $userService->getByEmail("{$socAuthHash}@soc-dummy.soc");

        if (is_null($userBySocAuthHash)) {
            $logger = Mif::getServiceProvider()->Logger;
            $logger->error("Could not find user by socAuthHash = {$socAuthHash}");
            return;
        }

        $userByEmail = $userService->getByEmail($email);

        Mif::getPersistHolder()->hold();
        if (is_null($userByEmail)) {
            $userService->addMailToAccount($userBySocAuthHash, $email);
        } else {
            /** @var SocNetworkUserData $socNetworkUserData */
            $socNetworkUserData = $userBySocAuthHash->getSocNetworks()->first();
            $socNetworkUserData->setUser($userByEmail);
            $userBySocAuthHash->remove();
        }
        Mif::getPersistHolder()->commit();
    }

    /**
     * @param ConfirmLink $currentLink
     * @return array
     */
    private function getTokensUserByCurrentLink(ConfirmLink $currentLink) : array
    {
        Mif::getPersistHolder()->hold();
        $accessToken = $currentLink->getAccessToken();
        $refreshToken = $currentLink->getRefreshToken();
        $confirmLinkService = $this->ConfirmLinkService;
        $confirmLinkService->activateConfirmLink($currentLink);
        $accessTokenService = $this->AccessTokenService;

        $accessTokenService->refreshExpireAndActivatedDates(
            $accessToken
        );

        $refreshTokenService = $this->RefreshTokenService;
        $refreshTokenService->refreshExpireAndActivatedDates(
            $refreshToken
        );

        $currentUser = $currentLink->getUser();
        $userService = $this->UserService;

        if (!$currentUser->getIsConfirmed()) {
            $userService->becameUserConfirmed($currentUser);
        }
        Mif::getPersistHolder()->commit();
        return [
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $refreshToken->getToken()
        ];
    }

    /**
     * @param AccessToken $accessToken
     * @param RefreshToken $refreshToken
     * @return string
     */
    private function generateConfirmToken(AccessToken $accessToken, RefreshToken $refreshToken) : string
    {
        return sha1($accessToken->getToken() . '_' . $refreshToken->getToken());
    }

    /**
     * @return int
     */
    private function generateCode() : int
    {
        return rand(1000, 9999);
    }

    /**
     * @param string|null $view
     * @param string $link
     * @param int $code
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function renderMessage(?string $view, string $link, int $code) : string
    {
        $template = '';
        $templating = $this->EnvironmentTwig;
        if (is_null($view)) {
            $template = 'message/default.html.twig';
        }

        return $templating->render($template, [
            'link' => $link,
            'code' => $code
        ]);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function isTwoFactor(User $user) : bool
    {
        $isTwoFactor = true;

        /** @var Role $roles */
        $roles = $user->getRoles();
        if (is_null($roles)) {
            return $isTwoFactor;
        }

        $typeTwoFactor = Mif::getEnvConfig('ON_ENV') ?? [];
        $isTwoFactor = $typeTwoFactor[Mif::getEnvConfig('APP_ENV')]['oauth2Settings']['twoFactor'] ?? true;
        if (is_array($isTwoFactor)) {
            $twoFactorArray = $isTwoFactor;
            $isTwoFactor = true;

            /** @var Role $role */
            foreach ($roles as $role) {
                $nameRole = $role->getName();
                if (array_key_exists($nameRole, $twoFactorArray)) {
                    if (!$twoFactorArray[$nameRole]) {
                        $isTwoFactor = $twoFactorArray[$nameRole];
                        break;
                    }
                }
            }
        }

        return $isTwoFactor;
    }

    /**
     * @param string $email
     * @return User
     * @throws NonUniqueResultException
     */
    private function getUserByEmail($email)
    {
        $userService = $this->UserService;
        $user = $userService->getByEmail($email);

        if (is_null($user)) {
            $user = $userService->newUser($email);
        }

        return $user;
    }
}
