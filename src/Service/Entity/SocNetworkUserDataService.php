<?php

namespace App\Service\Entity;

use App\Entity\Auth\SocNetworkUserData;
use App\Entity\User;
use App\Repository\Auth\SocNetworkUserDataRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class SocNetworkUserDataService
 * @package App\Service\Entity
 *
 * @method SocNetworkUserDataRepository getRepository()
 */
class SocNetworkUserDataService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return SocNetworkUserData::class;
    }

    /**
     * @param User $user
     * @param string $codeSocNet
     * @return SocNetworkUserData|null
     * @throws NonUniqueResultException
     */
    public function findOneByUser(User $user, string $codeSocNet) : ?SocNetworkUserData
    {
        return $this->getRepository()->findOneByUser($codeSocNet, $user);
    }

    /**
     * @param User $user
     * @param string $nameSocNet
     * @param string $idUserSocNet
     */
    public function create(User $user, string $nameSocNet, string $idUserSocNet) : void
    {
        $this->createEntity(
            [
                'code' => $nameSocNet,
                'socNetworkId' => $idUserSocNet,
                'user' => $user
            ]
        );
    }

    /**
     * @param SocNetworkUserData $networkUserData
     * @param string $idUserSocNet
     * @return void
     */
    public function update(SocNetworkUserData $networkUserData, string $idUserSocNet) : void
    {
        $networkUserData->setSocNetworkId($idUserSocNet);
        $networkUserData->save();
    }
}
