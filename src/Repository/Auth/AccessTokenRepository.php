<?php

namespace App\Repository\Auth;

use App\Entity\Auth\AccessToken;
use App\Entity\User;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method AccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessToken[]    findAll()
 * @method AccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessTokenRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    /**
     * @param User $user
     * @return AccessToken|null
     * @throws NonUniqueResultException
     */
   public function findByUser(User $user) : ?AccessToken
   {
       if ($user->isNew()) {
           return null;
       }

       return $this->createQueryBuilder('a')
           ->where('a.user = :user')
           ->setParameter('user', $user)
           ->getQuery()
           ->getOneOrNullResult();
   }

    /**
     * @param string $token
     * @return AccessToken|null
     * @throws NonUniqueResultException
     */
   public function getByActualToken(string $token) : ?AccessToken
   {
        return $this->createQueryBuilder('a')
        ->where('a.token = :token')
        ->andWhere('a.expire >= :nowDate')
        ->setParameter('token', $token)
        ->setParameter('nowDate', date('Y-m-d H:i:s'))
        ->getQuery()
        ->getOneOrNullResult();
   }

    /**
     * @param string $token
     * @return AccessToken|null
     * @throws NonUniqueResultException
     */
    public function getByToken(string $token) : ?AccessToken
    {
        return $this->createQueryBuilder('a')
            ->where('a.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
