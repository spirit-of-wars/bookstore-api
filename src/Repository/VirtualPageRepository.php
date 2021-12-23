<?php

namespace App\Repository;

use App\Entity\VirtualPage;
use App\Repository\BaseRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VirtualPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method VirtualPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method VirtualPage[]    findAll()
 * @method VirtualPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VirtualPageRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VirtualPage::class);
    }

    /**
     * @return mixed
     */
    public function getVirtualPagesIsMenu()
    {
        return $this->createQueryBuilder('p')
            ->where('p.isMenu = true')
            ->andWhere('p.level = 0')
            ->getQuery()
            ->getResult();
    }
}
