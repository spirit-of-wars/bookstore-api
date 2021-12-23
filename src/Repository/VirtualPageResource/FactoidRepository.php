<?php

namespace App\Repository\VirtualPageResource;

use App\Entity\VirtualPageResource\Factoid;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Factoid|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factoid|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factoid[]    findAll()
 * @method Factoid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactoidRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factoid::class);
    }

    /**
     * @param int $id
     * @return Factoid|null
     * @throws NonUniqueResultException
     */
    public function findById(int $id) : ?Factoid
    {
        return $this->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     * @return Factoid|null
     * @throws NonUniqueResultException
     */
    public function findByCode(string $code) : ?Factoid
    {
        return $this->createQueryBuilder('f')
            ->where('f.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
