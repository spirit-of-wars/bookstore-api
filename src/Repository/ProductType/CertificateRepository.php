<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Certificate;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Certificate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Certificate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Certificate[]    findAll()
 * @method Certificate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificateRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certificate::class);
    }

    // /**
    //  * @return Certificate[] Returns an array of Certificate objects
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
    public function findOneBySomeField($value): ?Certificate
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
