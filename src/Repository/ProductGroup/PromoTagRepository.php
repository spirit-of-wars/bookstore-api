<?php

namespace App\Repository\ProductGroup;

use App\Entity\ProductGroup\PromoTag;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method PromoTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoTag[]    findAll()
 * @method PromoTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoTagRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoTag::class);
    }

    /**
     * @param string $slug
     * @return PromoTag|null
     * @throws NonUniqueResultException
     */
    public function getBySlug(string $slug) : ?PromoTag
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return PromoTag|null
     * @throws NonUniqueResultException
     */
    public function getById(int $id) : ?PromoTag
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
