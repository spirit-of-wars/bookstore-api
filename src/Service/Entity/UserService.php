<?php

namespace App\Service\Entity;


use App\Entity\Auth\Role;
use App\Entity\User;
use App\Enum\UserRolesEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;


/**
 * Class UserService
 * @package App\Service\Entity
 *
 * @method UserRepository getRepository()
 * @method User createEntity($attributes)
 *
 * @property-read RoleService RoleService
 */
class UserService extends EntityService
{
    /**
     * @return array|string[]
     */
    public static function subscribedServicesMap() :array
    {
        return [
            'RoleService' => RoleService::class,
        ];
    }

    /**
     * @return string
     */
    public function getEntityClassName() : string
    {
        return User::class;
    }

    /**
     * @param string $email
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByEmail(string $email) : ?User
    {
        return $this->getRepository()->getByEmail($email);
    }

    /**
     * @param string $token
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByToken(string $token) : ?User
    {
        return $this->getRepository()->getByToken($token);
    }

    /**
     * @param int $vkId
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByVkId(int $vkId) : ?User
    {
        return $this->getRepository()->getByVkId($vkId);
    }

    /**
     * @param string $fbId
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByFbId(string $fbId) : ?User
    {
        return $this->getRepository()->getByFbId($fbId);
    }

    /**
     * @param string $socNetUserId
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByAppleId(string $socNetUserId) : ?User
    {
        return $this->getRepository()->getByAppleId($socNetUserId);
    }

    /**
     * @param string $id
     * @param string $codeSocNetwork
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getBySocNetId(string $id, string $codeSocNetwork) : ?User
    {
        return $this->getRepository()->getBySocNetId($id, $codeSocNetwork);
    }

    /**
     * @param string $email
     * @param bool $confirmedAccount
     * @param int|null $vkId
     * @param string|null $fbId
     * @param string|null $appleId
     * @return User
     */
    public function newUser(
        string $email,
        bool $confirmedAccount = false
    ) : User
    {
        $roleClient = $this->RoleService->getByName(UserRolesEnum::CLIENT);

        $attributes = [
            'email' => $email,
            'isConfirmed' => $confirmedAccount,
            'isActive' => true,
            'roles' => $roleClient
        ];

        return $this->createEntity($attributes);
    }

    /**
     * @param User $user
     */
    public function becameUserConfirmed(User $user) : void
    {
        $user->setIsConfirmed(true);
        $user->save();
    }

    /**
     * @param User $user
     * @param string $email
     */
    public function addMailToAccount(User $user, string $email) : void
    {
        $user->setEmail($email);
        $user->save();
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user) : void
    {
        $user->remove();
    }

    /**
     * @param User $user
     * @param Role $role
     */
    public function setRole(User $user, Role $role) : void
    {
        $user->addToRoles($role);
        $user->save();
    }

    /**
     * @param User $user
     * @param Role $role
     */
    public function removeRoles(User $user, Role $role) : void
    {
        $user->removeFromRoles($role);
        $user->save();
    }
}
