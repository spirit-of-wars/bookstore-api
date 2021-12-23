<?php

namespace App\Repository;

use App\Entity\CreativePartner;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CreativePartner|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreativePartner|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreativePartner[]    findAll()
 * @method CreativePartner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreativePartnerRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreativePartner::class);
    }

    // /**
    //  * @return CreativePartner[] Returns an array of CreativePartner objects
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
    public function findOneBySomeField($value): ?CreativePartner
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
