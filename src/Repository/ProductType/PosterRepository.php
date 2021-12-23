<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Poster;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Poster|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poster|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poster[]    findAll()
 * @method Poster[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PosterRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poster::class);
    }

    // /**
    //  * @return Poster[] Returns an array of Poster objects
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
    public function findOneBySomeField($value): ?Poster
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
