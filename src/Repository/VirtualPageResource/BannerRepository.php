<?php

namespace App\Repository\VirtualPageResource;

use App\Entity\VirtualPageResource\Banner;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Banner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banner[]    findAll()
 * @method Banner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banner::class);
    }

    /**
     * @param int $id
     * @return Banner|null
     * @throws NonUniqueResultException
     */
    public function findById(int $id) : ?Banner
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return Banner|null
     * @throws NonUniqueResultException
     */
    public function findBannerById(int $id) : ?Banner
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :id')
            ->andWhere('b.type = :type')
            ->setParameter('id', $id)
            ->setParameter('type', 'miniBanner')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function findBannersByIds(array $ids)
    {
        return $this->createQueryBuilder('b')
            ->where('b.id in (:ids)')
            ->andWhere('b.type = :type')
            ->setParameter('ids', $ids)
            ->setParameter('type', 'miniBanner')
            ->getQuery()
            ->getResult();
    }
}
