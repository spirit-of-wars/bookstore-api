<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\EBook;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method EBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method EBook[]    findAll()
 * @method EBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EBookRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EBook::class);
    }

    // /**
    //  * @return EBook[] Returns an array of EBook objects
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
    public function findOneBySomeField($value): ?EBook
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
