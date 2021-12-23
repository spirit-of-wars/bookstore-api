<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Bag;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Bag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bag[]    findAll()
 * @method Bag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BagRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bag::class);
    }

    // /**
    //  * @return Bag[] Returns an array of Bag objects
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
    public function findOneBySomeField($value): ?Bag
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
