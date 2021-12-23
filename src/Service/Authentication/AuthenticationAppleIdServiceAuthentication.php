<?php

namespace App\Service\Authentication;

use App\Entity\User;
use App\Enum\CodeSocNetworkEnum;
use App\Util\SocialNetwork\AppleApiClient;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class AuthenticationAppleIdServiceAuthentication
 * @package App\Service\Authentication
 */
class AuthenticationAppleIdServiceAuthentication extends AuthenticationSocNetService
{
    /**
     * @return string
     */
    protected function getApiClientClass(): string
    {
        return AppleApiClient::class;
    }

    /**
     * @param string $socNetUserId
     * @return User|null
     * @throws NonUniqueResultException
     */
    protected function getUserBySocNetId($socNetUserId): ?User
    {
        return $this->UserService->getBySocNetId($socNetUserId, CodeSocNetworkEnum::APPLE_ID);
    }

    /**
     * @param string $dummyEmail
     * @param bool $confirmedAccount
     * @return User
     */
    protected function newUser($dummyEmail, $confirmedAccount): User
    {
        return $this->UserService->newUser($dummyEmail, $confirmedAccount);
    }

    /**
     * @param User $user
     * @param string $socNetUserId
     */
    protected function addSocNetwork($user, $socNetUserId) : void
    {
        $this->SocNetworkUserDataService->create($user, CodeSocNetworkEnum::APPLE_ID, $socNetUserId);
    }

    /**
     * @param $user
     * @param $socNetUserId
     * @return User
     * @throws NonUniqueResultException
     */
    protected function actualizeUser($user, $socNetUserId): User
    {
        $socNetDataUser = $this->SocNetworkUserDataService->findOneByUser($user, CodeSocNetworkEnum::APPLE_ID);
        if (is_null($socNetDataUser)) {
            $this->SocNetworkUserDataService->create($user, CodeSocNetworkEnum::APPLE_ID, $socNetUserId);
        } else {
            $this->SocNetworkUserDataService->update($socNetDataUser, $socNetUserId);
        }

        return $user;
    }
}
