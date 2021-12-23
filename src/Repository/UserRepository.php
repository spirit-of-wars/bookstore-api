<?php

namespace App\Repository;

use App\Entity\Auth\Role;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $email
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByEmail(string $email) : ?User
    {
        return $this->createQueryBuilder('p')
            ->where('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $token
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByToken(string $token) : ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.accessToken', 'a')
            ->where('a.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $vkId
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByVkId(string $vkId) : ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.vkUserId = :vkId')
            ->setParameter('vkId', $vkId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $fbId
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getByFbId(string $fbId)
    {
        return $this->createQueryBuilder('u')
            ->where('u.fbUserId = :fbId')
            ->setParameter('fbId', $fbId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $socNetUserId
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByAppleId(string $socNetUserId) : ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.appleUserId = :socNetUserId')
            ->setParameter('socNetUserId', $socNetUserId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $id
     * @param string $codeSocNetwork
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getBySocNetId(string $id, string $codeSocNetwork) : ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.socNetworks', 's')
            ->where('s.code = :code')
            ->andWhere('s.socNetworkId = :id')
            ->setParameter('code', $codeSocNetwork)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
