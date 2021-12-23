<?php

namespace App\Repository\ProductEssence;

use App\Entity\ProductEssence\Essence;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Essence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Essence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Essence[]    findAll()
 * @method Essence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EssenceRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Essence::class);
    }
}
