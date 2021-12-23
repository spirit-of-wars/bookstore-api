<?php

namespace App\Service\Entity;

use App\Constants;
use App\Entity\Auth\AccessToken;
use App\Entity\Auth\ConfirmLink;
use App\Entity\Auth\RefreshToken;
use App\Entity\User;
use App\Helper\TokenHelper;
use App\Mif;
use App\Repository\Auth\ConfirmLinkRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class ConfirmLinkService
 * @package App\Service\Entity
 *
 * @method ConfirmLinkRepository getRepository()
 * @method ConfirmLink createEntity($attributes)
 */
class ConfirmLinkService extends EntityService
{
    public function getEntityClassName()
    {
        return ConfirmLink::class;
    }

    /**
     * @param User $user
     * @return ConfirmLink|null
     * @throws NonUniqueResultException
     */
    public function findByUser(User $user) : ?ConfirmLink
    {
        return $this->getRepository()->findByUser($user);
    }

    /**
     * @param User $user
     * @param AccessToken $currentAccessToken
     * @param RefreshToken $currentRefreshToken
     * @param string $token
     * @param int $code
     * @throws NonUniqueResultException
     */
    public function prepareForLink(
        User $user,
        AccessToken $currentAccessToken,
        RefreshToken $currentRefreshToken,
        string $token,
        int $code
    ) : void
    {
        $activateLink = $this->findByUser($user);

        if (is_null($activateLink)) {
            $this->newLink(
                $user,
                $currentAccessToken,
                $currentRefreshToken,
                $token,
                $code
            );
        } else {
            $this->updateLink(
                $activateLink,
                $token,
                $code
            );
        }
    }

    /**
     * @param string $token
     * @return ConfirmLink|null
     * @throws NonUniqueResultException
     */
    public function getByToken(string $token) :?ConfirmLink
    {
        return $this->getRepository()->getByToken($token);
    }

    /**
     * @param int $code
     * @return ConfirmLink|null
     * @throws NonUniqueResultException
     */
    public function getByCode(int $code) :?ConfirmLink
    {
        return $this->getRepository()->getByCode($code);
    }

    /**
     * @param User $user
     * @param AccessToken $accessToken
     * @param RefreshToken $refreshToken
     * @param string $token
     * @param int $code
     * @return ConfirmLink
     */
    public function newLink(
        User $user,
        AccessToken $accessToken,
        RefreshToken $refreshToken,
        string $token,
        int $code
    ) : ConfirmLink
    {
        return $this->createEntity([
            'token' => $token,
            'isActivated' => false,
            'expire' => TokenHelper::calculateExpire(
                Mif::getEnvConfig(
                    Constants::CK_LIFETIME_ACTIVATE_LINK) ??
                    Constants::DEFAULT_LIFETIME_ACTIVATE_LINK
            ),
            'refreshToken' => $refreshToken,
            'accessToken' => $accessToken,
            'user' => $user,
            'code' => $code
        ]);
    }

    /**
     * @param ConfirmLink $activateLink
     * @param string $token
     * @param int $code
     * @param bool $isActivated
     * @return ConfirmLink
     */
    public function updateLink(
        ConfirmLink $activateLink,
        string $token,
        int $code,
        bool $isActivated = false
    ) : ConfirmLink
    {
        $activateLink->setProperties([
            'token' => $token,
            'isActivated' => $isActivated,
            'code' => $code,
            'expire' => TokenHelper::calculateExpire(
                Mif::getEnvConfig(Constants::CK_LIFETIME_ACTIVATE_LINK) ??
                Constants::DEFAULT_LIFETIME_ACTIVATE_LINK)
        ]);

        $activateLink->save();

        return $activateLink;
    }

    /**
     * @param ConfirmLink $activateLink
     */
    public function activateConfirmLink(ConfirmLink $activateLink) : void
    {
        $currentActivatedLink = $activateLink->setIsActivated(true);
        $currentActivatedLink->save();
    }
}
