<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\PaperBook;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PaperBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaperBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaperBook[]    findAll()
 * @method PaperBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaperBookRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaperBook::class);
    }

    // /**
    //  * @return PaperBook[] Returns an array of PaperBook objects
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
    public function findOneBySomeField($value): ?PaperBook
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
