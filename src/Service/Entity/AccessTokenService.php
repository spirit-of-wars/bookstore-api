<?php

namespace App\Service\Entity;

use App\Constants;
use App\Helper\TokenHelper;
use App\Entity\Auth\AccessToken;
use App\Entity\User;
use App\Mif;
use App\Repository\Auth\AccessTokenRepository;
use Doctrine\ORM\NonUniqueResultException;
use DateTime;

/**
 * Class AccessTokenService
 * @package App\Service\Entity
 *
 * @method AccessToken getNewEntityInstance($attributes = [])
 * @method AccessToken createEntity($attributes = [])
 * @method AccessTokenRepository getRepository()
 */
class AccessTokenService extends EntityService
{
    /**
     * @return string
     */
    public function getEntityClassName()
    {
        return AccessToken::class;
    }

    /**
     * @param User $user
     * @return AccessToken|null
     * @throws NonUniqueResultException
     */
    public function findByUser(User $user) : ?AccessToken
    {
        return $this->getRepository()->findByUser($user);
    }

    /**
     * @param User $user
     * @param bool $updateActivateToken
     * @return AccessToken
     * @throws NonUniqueResultException
     */
    public function prepareForUser(User $user, bool $updateActivateToken = true) : AccessToken
    {
        $accessToken = $this->findByUser($user);

        if (is_null($accessToken)) {
            $currentAccessToken = $this->newToken($user);
        } else {
            $currentAccessToken = $this->refreshExpireAndActivatedDates(
                $accessToken,
                $updateActivateToken
            );
        }

        return $currentAccessToken;
    }

    /**
     * @param string $token
     * @return AccessToken|null
     * @throws NonUniqueResultException
     */
    public function getByActualToken(string $token) : ?AccessToken
    {
        return $this->getRepository()->getByActualToken($token);
    }

    /**
     * @param string $token
     * @return AccessToken|null
     * @throws NonUniqueResultException
     */
    public function getByToken(string $token) : ?AccessToken
    {
        return $this->getRepository()->getByToken($token);
    }

        /**
     * @param User $user
     * @return AccessToken
     */
    public function newToken(User $user) : AccessToken
    {
        return $this->createEntity([
            'user'  => $user,
            'token'  => TokenHelper::generateToken(),
            'expire' => TokenHelper::calculateExpire(
                Mif::getEnvConfig(Constants::CK_LIFETIME_ACCESS_TOKEN) ??
                Constants::DEFAULT_LIFETIME_ACCESS_TOKEN
            ),
        ]);
    }

    /**
     * @param AccessToken $accessToken
     * @param bool $updateActivateToken
     * @param DateTime|null $activatedAt
     * @return AccessToken
     */
    public function refreshExpireAndActivatedDates(
        AccessToken $accessToken,
        bool $updateActivateToken = true,
        DateTime $activatedAt = null
    ) : AccessToken
    {
        if (is_null($activatedAt) && $updateActivateToken) {
            $activatedAt = new DateTime();
            $activatedAt->format('Y-m-d H:i:s');
        }

        $accessToken->setProperties([
            'token'  => TokenHelper::generateToken(),
            'activatedAt' => $activatedAt,
            'expire' => TokenHelper::calculateExpire(
                Mif::getEnvConfig(Constants::CK_LIFETIME_ACCESS_TOKEN) ??
                Constants::DEFAULT_LIFETIME_ACCESS_TOKEN
            ),
        ]);

        $accessToken->save();

        return $accessToken;
    }
}
