<?php

namespace App\Repository\Auth;

use App\Entity\Auth\ConfirmLink;
use App\Entity\User;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method ConfirmLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfirmLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfirmLink[]    findAll()
 * @method ConfirmLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfirmLinkRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfirmLink::class);
    }

    /**
     * @param User $user
     * @return ConfirmLink|null
     * @throws NonUniqueResultException
     */
    public function findByUser(User $user) : ?ConfirmLink
    {
        if ($user->isNew()) {
            return null;
        }

        return $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $token
     * @return ConfirmLink|null
     * @throws NonUniqueResultException
     */
    public function getByToken(string $token) : ?ConfirmLink
    {
        return $this->createQueryBuilder('l')
            ->where('l.token = :token')
            ->andWhere('l.expire >= :expire')
            ->andWhere('l.isActivated = false')
            ->setParameter('token', $token)
            ->setParameter('expire', date('Y-m-d H:i:s'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $code
     * @return ConfirmLink|null
     * @throws NonUniqueResultException
     */
    public function getByCode(int $code) : ?ConfirmLink
    {
        return $this->createQueryBuilder('l')
            ->where('l.code = :code')
            ->andWhere('l.expire >= :expire')
            ->andWhere('l.isActivated = false')
            ->setParameter('code', $code)
            ->setParameter('expire', date('Y-m-d H:i:s'))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
