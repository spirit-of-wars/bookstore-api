<?php

namespace App\Repository\Auth;

use App\Entity\Auth\RefreshToken;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll()
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshTokenRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    /**
     * @param User $user
     * @return RefreshToken|null
     * @throws NonUniqueResultException
     */
    public function findByUser(User $user) : ?RefreshToken
    {
        if ($user->isNew()) {
            return null;
        }

        return $this->createQueryBuilder('r')
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $token
     * @return RefreshToken|null
     * @throws NonUniqueResultException
     */
    public function getByActualToken(string $token) :?RefreshToken
    {
        return $this->createQueryBuilder('r')
            ->where('r.token = :token')
            ->andWhere('r.expire >= :expire')
            ->setParameter('token', $token)
            ->setParameter('expire',  date('Y-m-d H:i:s'))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
