<?php

namespace App\Repository\Auth;

use App\Entity\Auth\SocNetworkUserData;
use App\Entity\User;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method SocNetworkUserData|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocNetworkUserData|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocNetworkUserData[]    findAll()
 * @method SocNetworkUserData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocNetworkUserDataRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocNetworkUserData::class);
    }

    /**
     * @param string $codeSocNet
     * @param User $user
     * @return SocNetworkUserData|null
     * @throws NonUniqueResultException
     */
    public function findOneByUser(string $codeSocNet, User $user) : ?SocNetworkUserData
    {
        return $this->createQueryBuilder('s')
            ->where('s.code = :codeSocNet')
            ->andWhere('s.user = :user')
            ->setParameter('codeSocNet', $codeSocNet)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
