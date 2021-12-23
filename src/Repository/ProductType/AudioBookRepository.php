<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\AudioBook;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AudioBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method AudioBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method AudioBook[]    findAll()
 * @method AudioBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudioBookRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AudioBook::class);
    }

    // /**
    //  * @return AudioBook[] Returns an array of AudioBook objects
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
    public function findOneBySomeField($value): ?AudioBook
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
