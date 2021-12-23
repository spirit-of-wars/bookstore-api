<?php

namespace App\Repository\VirtualPageResource;

use App\Entity\VirtualPageResource\BannerShelf;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method BannerShelf|null find($id, $lockMode = null, $lockVersion = null)
 * @method BannerShelf|null findOneBy(array $criteria, array $orderBy = null)
 * @method BannerShelf[]    findAll()
 * @method BannerShelf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerShelfRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BannerShelf::class);
    }

    /**
     * @param int $id
     * @return BannerShelf|null
     * @throws NonUniqueResultException
     */
    public function findById(int $id) : ?BannerShelf
    {
        return $this->createQueryBuilder('sb')
            ->where('sb.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     * @return BannerShelf|null
     * @throws NonUniqueResultException
     */
    public function findByCode(string $code) : ?BannerShelf
    {
        return $this->createQueryBuilder('sb')
            ->where('sb.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
