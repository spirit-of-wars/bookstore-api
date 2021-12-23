<?php

namespace App\Repository\ProductData;

use App\Entity\Product;
use App\Entity\ProductData\Data1C;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Data1C|null find($id, $lockMode = null, $lockVersion = null)
 * @method Data1C|null findOneBy(array $criteria, array $orderBy = null)
 * @method Data1C[]    findAll()
 * @method Data1C[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Data1CRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Data1C::class);
    }

    public function getByProduct(Product $product) : ?Data1C
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.product = :product')
            ->andWhere('d.isActive = true')
            ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
