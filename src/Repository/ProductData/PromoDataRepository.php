<?php

namespace App\Repository\ProductData;

use App\Entity\ProductData\PromoData;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PromoData|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoData|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoData[]    findAll()
 * @method PromoData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoDataRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoData::class);
    }

    // /**
    //  * @return PromoData[] Returns an array of PromoData objects
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
    public function findOneBySomeField($value): ?PromoData
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
