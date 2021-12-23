<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Sticker;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sticker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sticker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sticker[]    findAll()
 * @method Sticker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StickerRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sticker::class);
    }

    // /**
    //  * @return Sticker[] Returns an array of Sticker objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sticker
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
