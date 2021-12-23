<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Postcard;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Postcard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postcard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postcard[]    findAll()
 * @method Postcard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostcardRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postcard::class);
    }

    // /**
    //  * @return Postcard[] Returns an array of Postcard objects
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
    public function findOneBySomeField($value): ?Postcard
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
