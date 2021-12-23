<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Kit;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Kit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kit[]    findAll()
 * @method Kit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KitRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kit::class);
    }

    // /**
    //  * @return Kit[] Returns an array of Kit objects
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
    public function findOneBySomeField($value): ?Kit
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
