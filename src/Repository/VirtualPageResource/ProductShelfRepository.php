<?php

namespace App\Repository\VirtualPageResource;

use App\Entity\VirtualPageResource\ProductShelf;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method ProductShelf|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductShelf|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductShelf[]    findAll()
 * @method ProductShelf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductShelfRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductShelf::class);
    }

    /**
     * @param string $name
     * @param string $code
     * @param string $type
     * @return ProductShelf|null
     * @throws NonUniqueResultException
     */
    public function getDuplicate(string $name, string $code, string $type) : ?ProductShelf
    {
        return $this->createQueryBuilder('s')
            ->where('s.name = :name')
            ->andWhere('s.code = :code')
            ->andWhere('s.type = :type')
            ->setParameter('name', $name)
            ->setParameter('code', $code)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return ProductShelf|null
     * @throws NonUniqueResultException
     */
    public function getArrayShelfAndProductById(int $id) : ?ProductShelf
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     * @return ProductShelf|null
     * @throws NonUniqueResultException
     */
    public function getByCode(string $code) : ?ProductShelf
    {
        return $this->createQueryBuilder('s')
            ->where('s.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $productShelvesPromoTagIds
     * @return array
     */
    public function findByPromoTagIds(array $productShelvesPromoTagIds) : array
    {
        return $this->createQueryBuilder('s')
            ->where('s.id IN (:productShelvesPromoTagIds)')
            ->setParameter('productShelvesPromoTagIds', $productShelvesPromoTagIds)
            ->getQuery()
            ->getResult();
    }
}
