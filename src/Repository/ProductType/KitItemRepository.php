<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\KitItem;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method KitItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method KitItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method KitItem[]    findAll()
 * @method KitItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KitItemRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KitItem::class);
    }

    // /**
    //  * @return KitItem[] Returns an array of KitItem objects
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
    public function findOneBySomeField($value): ?KitItem
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
