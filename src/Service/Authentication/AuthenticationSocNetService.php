<?php

namespace App\Service\Authentication;

use App\Entity\User;
use App\Mif;
use App\Service\Entity\AccessTokenService;
use App\Service\Entity\SocNetworkUserDataService;
use App\Service\Entity\UserService;
use App\Service\Entity\RefreshTokenService;
use App\Service\Service;
use App\Util\SocialNetwork\SocNetApiClientInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class AuthenticationVkServiceAuthentication
 * @package App\Service\Authentication
 *
 * @property-read UserService UserService
 * @property-read AccessTokenService AccessTokenService
 * @property-read RefreshTokenService RefreshTokenService
 * @property-read SocNetworkUserDataService SocNetworkUserDataService
 */
abstract class AuthenticationSocNetService extends Service
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
            'SocNetworkUserDataService' => SocNetworkUserDataService::class,
        ];
    }

    /**
     * @return string
     */
    abstract protected function getApiClientClass() : string;

    /**
     * @param $socNetUserId
     * @return User|null
     */
    abstract protected function getUserBySocNetId($socNetUserId) : ?User;

    /**
     * @param string $dummyEmail
     * @param bool $confirmedAccount
     * @return User
     */
    abstract protected function newUser($dummyEmail, $confirmedAccount) : User;

    /**
     * @param User $user
     * @param string $socNetUserId
     */
    abstract protected function addSocNetwork($user, $socNetUserId) : void;

    /**
     * @param $user
     * @param $socNetUserId
     * @return User
     */
    abstract protected function actualizeUser($user, $socNetUserId) : User;

    /**
     * @param array $authData
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function generateTokensForUser(array $authData) : ?array
    {
        $socNetApiClient = $this->getApiClient();
        $socNetApiClient->requestForUserData($authData['code'], $authData['redirectUri'] ?? null);

        if ($socNetApiClient->hasErrors()) {
            if (is_array($socNetApiClient->getErrors())) {
                foreach ($socNetApiClient->getErrors() as $error) {
                    Mif::getServiceProvider()->Logger->error($error);
                }
            } else {
                Mif::getServiceProvider()->Logger->error(implode("\n", $socNetApiClient->getErrors()));
            }

            return null;
        }

        return $this->authentication(
            $socNetApiClient->getUserId(),
            $socNetApiClient->getUserEmail()
        );
    }

    /**
     * @param string $token
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function checkUserData(string $token) : ?array
    {
        $socNetworkClient = $this->getApiClient();
        $socNetworkClient->receiveUserIdByToken($token);
        return $this->authentication($socNetworkClient->getUserId(), $socNetworkClient->getUserEmail());
    }

    /**
     * @param int|string $socNetUserId
     * @param string|null $emailFromRequest
     * @return array|null
     * @throws NonUniqueResultException
     */
    private function authentication($socNetUserId, ?string $emailFromRequest) : ?array
    {
        $userService = $this->UserService;


        if (is_null($emailFromRequest)) {
            $user = $this->getUserBySocNetId($socNetUserId);
        } else {
            $user = $userService->getByEmail($emailFromRequest);
        }

        $hash = sha1($socNetUserId);

        if (is_null($user) && is_null($emailFromRequest)) {
            $user = $this->newUser($hash . '@soc-dummy.soc', false);
            $this->addSocNetwork($user, $socNetUserId);
            Mif::getServiceProvider()->Logger->error("Unauthorized user {$hash}! Email required.");
            return ['hashMailUsr' => $hash];
        }

        if (is_null($user)) {
            $user = $this->newUser($emailFromRequest, true);
            $this->addSocNetwork($user, $socNetUserId);
        } else {
            $user = $this->actualizeUser($user, $socNetUserId);
        }

        $accessToken = $this->AccessTokenService->prepareForUser($user);
        $refreshToken = $this->RefreshTokenService->prepareForUser($user);

        return ['accessToken' => $accessToken->getToken(), 'refreshToken' => $refreshToken->getToken()];
    }

    /**
     * @return SocNetApiClientInterface
     */
    private function getApiClient() : SocNetApiClientInterface
    {
        $className = $this->getApiClientClass();
        return new $className;
    }
}
