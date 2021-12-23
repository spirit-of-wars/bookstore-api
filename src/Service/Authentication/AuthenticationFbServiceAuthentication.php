<?php

namespace App\Service\Authentication;

use App\Entity\User;
use App\Enum\CodeSocNetworkEnum;
use App\Util\SocialNetwork\FbApiClient;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class AuthenticationFbServiceAuthentication
 * @package App\Service\Authentication
 */
class AuthenticationFbServiceAuthentication extends AuthenticationSocNetService
{
    /**
     * @return string
     */
    protected function getApiClientClass() : string
    {
        return FbApiClient::class;
    }

    /**
     * @param $socNetUserId
     * @return User|null
     * @throws NonUniqueResultException
     */
    protected function getUserBySocNetId($socNetUserId) : ?User
    {
        return $this->UserService->getBySocNetId($socNetUserId, CodeSocNetworkEnum::FB);
    }

    /**
     * @param string $dummyEmail
     * @param bool $confirmedAccount
     * @return User
     */
    protected function newUser($dummyEmail, $confirmedAccount) : User
    {
        return $this->UserService->newUser($dummyEmail, $confirmedAccount);
    }

    /**
     * @param User $user
     * @param string $socNetUserId
     */
    protected function addSocNetwork($user, $socNetUserId) : void
    {
        $this->SocNetworkUserDataService->create($user, CodeSocNetworkEnum::FB, $socNetUserId);
    }

    /**
     * @param User $user
     * @param int|string $socNetUserId
     * @return User
     * @throws NonUniqueResultException
     */
    protected function actualizeUser($user, $socNetUserId) : User
    {
        $socNetDataUser = $this->SocNetworkUserDataService->findOneByUser($user, CodeSocNetworkEnum::FB);
        if (is_null($socNetDataUser)) {
            $this->SocNetworkUserDataService->create($user, CodeSocNetworkEnum::FB, $socNetUserId);
        } else {
            $this->SocNetworkUserDataService->update($socNetDataUser, $socNetUserId);
        }

        return $user;
    }
}
