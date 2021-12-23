<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Notepad;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Notepad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notepad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notepad[]    findAll()
 * @method Notepad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotepadRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notepad::class);
    }

    // /**
    //  * @return Notepad[] Returns an array of Notepad objects
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
    public function findOneBySomeField($value): ?Notepad
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
