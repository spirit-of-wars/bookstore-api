<?php

namespace App\Service\Authentication;

use App\Entity\User;
use App\Enum\CodeSocNetworkEnum;
use App\Util\SocialNetwork\VkApiClient;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class AuthenticationVkServiceAuthentication
 * @package App\Service\Authentication
 */
class AuthenticationVkServiceAuthentication extends AuthenticationSocNetService
{
    /**
     * @return string
     */
    protected function getApiClientClass() : string
    {
        return VkApiClient::class;
    }

    /**
     * @param $socNetUserId
     * @return User|null
     * @throws NonUniqueResultException
     */
    protected function getUserBySocNetId($socNetUserId) : ?User
    {
        return $this->UserService->getBySocNetId($socNetUserId, CodeSocNetworkEnum::VK);
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
        $this->SocNetworkUserDataService->create($user, CodeSocNetworkEnum::VK, $socNetUserId);
    }

    /**
     * @param User $user
     * @param int|string $socNetUserId
     * @return User
     * @throws NonUniqueResultException
     */
    protected function actualizeUser($user, $socNetUserId) : User
    {
        $socNetDataUser = $this->SocNetworkUserDataService->findOneByUser($user, CodeSocNetworkEnum::VK);
        if (is_null($socNetDataUser)) {
            $this->SocNetworkUserDataService->create($user, CodeSocNetworkEnum::VK, $socNetUserId);
        } else {
            $this->SocNetworkUserDataService->update($socNetDataUser, $socNetUserId);
        }

        return $user;
    }
}
