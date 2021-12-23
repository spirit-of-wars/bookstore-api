<?php

namespace App\Service\Entity;

use App\Constants;
use App\Entity\Auth\RefreshToken;
use App\Entity\User;
use App\Helper\TokenHelper;
use App\Mif;
use App\Repository\Auth\RefreshTokenRepository;
use Doctrine\ORM\NonUniqueResultException;
use DateTime;

/**
 * Class RefreshTokenService
 * @package App\Service\Entity
 *
 * @method RefreshToken getNewEntityInstance($attributes = [])
 * @method RefreshToken createEntity($attributes = [])
 * @method RefreshTokenRepository getRepository()
 */
class RefreshTokenService extends EntityService
{
    public function getEntityClassName()
    {
        return RefreshToken::class;
    }

    /**
     * @param User $user
     * @return RefreshToken|null
     * @throws NonUniqueResultException
     */
    public function findByUser(User $user) : ?RefreshToken
    {
        return $this->getRepository()->findByUser($user);
    }

    /**
     * @param User $user
     * @param bool $updateActivateToken
     * @return RefreshToken
     * @throws NonUniqueResultException
     */
    public function prepareForUser(User $user, bool $updateActivateToken = true) : RefreshToken
    {
        $token = $this->findByUser($user);

        if (is_null($token)) {
            $currentToken = $this->newToken($user);
        } else {
            $currentToken = $this->refreshExpireAndActivatedDates($token, $updateActivateToken);
        }

        return $currentToken;
    }

    /**
     * @param string $token
     * @return RefreshToken|null
     * @throws NonUniqueResultException
     */
    public function getByActualToken(string $token) : ?RefreshToken
    {
        return $this->getRepository()->getByActualToken($token);
    }

    /**
     * @param User $user
     * @return RefreshToken
     */
    public function newToken(User $user) : RefreshToken
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
     * @param RefreshToken $refreshToken
     * @param bool $updateActivateToken
     * @param DateTime|null $activatedAt
     * @return RefreshToken
     */
    public function refreshExpireAndActivatedDates(
        RefreshToken $refreshToken,
        bool $updateActivateToken = true,
        DateTime $activatedAt = null
    ) : RefreshToken
    {
        if (is_null($activatedAt) && $updateActivateToken) {
            $activatedAt = new DateTime();
            $activatedAt->format('Y-m-d H:i:s');
        }

        $refreshToken->setProperties([
            'token'  => TokenHelper::generateToken(),
            'activatedAt' => $activatedAt,
            'expire' => TokenHelper::calculateExpire(
                Mif::getEnvConfig(Constants::CK_LIFETIME_REFRESH_TOKEN) ??
                Constants::DEFAULT_LIFETIME_REFRESH_TOKEN
            ),
        ]);

        $refreshToken->save();

        return $refreshToken;
    }
}
