<?php

namespace App\Repository\ProductEssence;

use App\Entity\ProductEssence\Book;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param $bookIds
     * @return array
     */
    public function findByIds($bookIds) : array
    {
       return $this->createQueryBuilder('b')
            ->where('b.id IN (:bookIds)')
            ->setParameter('bookIds', $bookIds)
            ->getQuery()
            ->getResult();
    }
}
