<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductType\PaperBook;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * TODO - метод не используем, оставили в демонстрационных целях. В запросе игнорится ленивая загрузка
     *
     * @param string $nameProduct
     * @param string $isbn
     * @return Product|null
     */
    public function getWithActiveData1CByFullNameAndIsbn(string $nameProduct, string $isbn) : ?array
    {
        $arrProduct = $this->_em->createQuery(
            'SELECT p, d
                FROM App\Entity\Product p
                LEFT JOIN p.data1C d
                WHERE d.isbn = :isbn and LOWER(p.fullName) = LOWER(:nameProduct) and d.isActive = true'
            )->setParameter('isbn', $isbn)
            ->setParameter('nameProduct', $nameProduct)
            ->getResult();

        // TODO исправить null на вызов исключения
        if (count($arrProduct) > 1) {
            return null;
        }

        if (count($arrProduct) == 1) {
            return [$arrProduct[0], $arrProduct[0]->getData1C()];
        }

        return null;
    }

    /**
     * @param string $fullName
     * @param string $isbn
     * @return Product|null
     */
    public function getByFullNameAndIsbn(string $fullName, string $isbn) :?Product
    {
        $arrProduct = $this->createQueryBuilder('p')
            ->leftJoin('p.data1C', 'd')
            ->andWhere('LOWER(p.fullName) = LOWER(:fullName)')
            ->andWhere('d.isActive = true')
            ->andWhere('d.isbn = :isbn')
            ->setParameter('fullName', $fullName)
            ->setParameter('isbn', $isbn)
            ->getQuery()
            ->getResult();

        // TODO исправить null на вызов исключения
        if (count($arrProduct) > 1) {
            return null;
        }

        if (count($arrProduct) == 1) {
            return $arrProduct[0];
        }
        return null;
    }

    /**
     * @param string $nameProduct
     * @return Product
     */
    public function getByFullName(string $nameProduct) : ?Product
    {
        $arrProduct = $this->createQueryBuilder('p')
            ->andWhere('LOWER(p.fullName) = LOWER(:nameProduct)')
            ->setParameter('nameProduct', $nameProduct)
            ->getQuery()
            ->getResult();

        // TODO исправить null на вызов исключения
        if (count($arrProduct) > 1) {
            return null;
        }

        if (count($arrProduct) === 1) {
            return $arrProduct[0];
        }

        return null;
    }

     /**
     * @param array $ids
     * @return mixed
     */
    public function findByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $series
     * @return array
     */
    public function findBySeries($series) : array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.series', 's')
            ->where('s.id = :series')
            ->setParameter('series', $series)
            ->getQuery()
            ->getResult();
    }

    /**
     * Adding simple search by fields to QueryBuilder $qb
     * @param QueryBuilder $qb
     * @param $query
     * @return QueryBuilder $qb
     */
    protected function simpleSearchByFields($qb, $query)
    {
        $query = mb_strtolower($query);
        // search in product table
        $sql = "lower(e.slug) LIKE '%$query%'";
        if ((integer)$query) {
            $sql .= "OR e.id = $query";
        }
        $qb->andWhere($sql);

        // search in essence table
        $qb->leftJoin('e.essence', 'es')
            ->orWhere("lower(es.fullName) LIKE '%$query%'");

        // search in data_1c table
        $qb->leftJoin('e.data1C', 'c', 'WITH', 'c.isActive = true')
            ->orWhere("lower(c.id1c) LIKE '%$query%' OR lower(c.isbn) LIKE '%$query%'");

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $sortType
     * @param string $sortOrder
     * @return QueryBuilder $qb
     */
    protected function setQueryOrder($qb, $sortType, $sortOrder)
    {
        $selfFields = ['type', 'lifeCycleStatus', 'startSaleDate', 'price'];
        if (in_array($sortType, $selfFields)) {
            $qb->orderBy("e.$sortType", $sortOrder);
            return $qb;
        }

        $fields1C = ['id1c', 'isbn', 'rrPrice'];
        if (in_array($sortType, $fields1C)) {
            if (!in_array('c', $qb->getAllAliases())) {
                $qb->leftJoin('e.data1C', 'c', 'WITH', 'c.isActive = true');
            }

            $qb->orderBy("c.$sortType", $sortOrder);
            return $qb;
        }

        if ($sortType == 'name') {
            if (!in_array('es', $qb->getAllAliases())) {
                $qb->leftJoin('e.essence', 'es');
            }

            $qb->orderBy('es.fullName', $sortOrder);
            return $qb;
        }

        if ($sortType == 'priceLabyrinth') {
            $qb->leftJoin(PaperBook::class, 'pb', 'WITH', 'pb.product = e');
            $qb->orderBy('pb.priceLabyrinth', $sortOrder);
        }

        return $qb;
    }
}
