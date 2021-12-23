<?php

namespace App\Repository\VirtualPageResource;

use App\Entity\VirtualPageResource\FreeBlock;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FreeBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreeBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreeBlock[]    findAll()
 * @method FreeBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreeBlockRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreeBlock::class);
    }

    // /**
    //  * @return FreeBlock[] Returns an array of FreeBlock objects
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
    public function findOneBySomeField($value): ?FreeBlock
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
