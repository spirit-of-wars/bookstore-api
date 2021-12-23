<?php

namespace App\Repository\ProductType;

use App\Entity\ProductType\Game;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @param array $gameIds
     * @return array
     */
    public function findByIds(array $gameIds) : array
    {
        return $this->createQueryBuilder('g')
            ->where('g.id IN (:gameIds)')
            ->setParameter('gameIds', $gameIds)
            ->getQuery()
            ->getResult();
    }
}
