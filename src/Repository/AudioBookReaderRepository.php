<?php

namespace App\Repository;

use App\Entity\AudioBookReader;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AudioBookReader|null find($id, $lockMode = null, $lockVersion = null)
 * @method AudioBookReader|null findOneBy(array $criteria, array $orderBy = null)
 * @method AudioBookReader[]    findAll()
 * @method AudioBookReader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudioBookReaderRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AudioBookReader::class);
    }

    // /**
    //  * @return AudioBookReader[] Returns an array of AudioBookReader objects
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
    public function findOneBySomeField($value): ?AudioBookReader
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
