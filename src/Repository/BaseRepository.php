<?php
namespace App\Repository;

use App\Constants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BaseRepository extends ServiceEntityRepository
{
    /**
     * Returns count for all object records
     * @return integer
     */
    public function countAll()
    {
        return $this->count([]);
    }

    /**
     * Adding pagination to QueryBuilder $qb
     * @param QueryBuilder $qb
     * @param integer $page
     * @param integer $limit
     * @return QueryBuilder $qb
     */
    public function paginator(QueryBuilder $qb, int $page = 1, int $limit = Constants::PAGE_LIMIT)
    {
        $offset = $page * $limit;
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb;
    }

    /**
     * TODO название метода не нравится. И вообще это свалка условий поиска
     *
     * @param array $attributes
     * @param array $options
     * @return array
     */
    public function findByAttributeFilters($attributes = [], $options = [])
    {
        $qb = $this->createQueryBuilder('e');
        $page = $options['page'] ?? 0;
        $limit = $options['limit'] ?? Constants::PAGE_LIMIT;
        $sortType = $options['sortType'] ?? null;
        $sortOrder = $options['sortOrder'] ?? Constants::DEFAULT_SORT_ORDER;
        $query = $options['query'] ?? null;

        if ($query) {
            $qb = $this->simpleSearchByFields($qb, $query);
        }

        $i = 0;
        if (!empty($attributes)) {
            foreach ($attributes as $attributeName => $attribute) {
                $i++;
                if (is_array($attribute)) {
                    $qb->andWhere('e.' . $attributeName . " IN (:val{$i})")
                        ->setParameter("val{$i}", $attribute);
                } else {
                    $qb->andWhere('e.' . $attributeName . " = :val{$i}")
                        ->setParameter("val{$i}", $attribute);
                }
            }
        }

        $qb->select('count(e) as count');
        $count = $qb->getQuery()->getResult()[0]['count'];

        if ($sortType) {
            if ($sortType == 'id') {
                $qb->orderBy('e.id', $sortOrder);
            } else {
                $qb = $this->setQueryOrder($qb, $sortType, $sortOrder);
                $qb->addOrderBy('e.id', $sortOrder);
            }
        }

        $qb->select('e');
        $qb = $this->paginator($qb, $page, $limit);
        $list = $qb->getQuery()->getResult();

        return [
            'count' => $count,
            'list' => $list,
        ];
    }

    /**
     * @param $alias
     * @param array $joins
     * @param array $conditions
     * @param array $sortBy
     * @param null $page
     * @param null $limit
     * @return int|mixed|string
     */
    public function getEntityByAttributesAndSort(
        $alias,
        $joins = [],
        $conditions = [],
        $sortBy = [],
        $page = null,
        $limit = null
    )
    {
        $qb = $this->createQueryBuilder($alias);
        if (!empty($joins)) {
            foreach ($joins as $aliasJoin => $relation ) {
                $qb->leftJoin("{$alias}.{$relation}", $aliasJoin);
            }
        }

        $i = 0;
        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                $i++;
                $aliasParameter = "val{$i}";
                $aliasRelation = stristr($condition[0], '.', true);
                if ($condition[2] === 'MEMBER OF') {
                    $qb->andWhere(":{$aliasParameter} {$condition[2]} {$condition[0]}")
                        ->setParameter($aliasParameter, $condition[1]);
                    continue;
                }

                if ($aliasRelation) {
                    $qb->andWhere("{$condition[0]} {$condition[2]} :{$aliasParameter}")
                        ->setParameter($aliasParameter, $condition[1]);
                } else {
                    $qb->andWhere("{$alias}.{$condition[0]} {$condition[2]} :{$aliasParameter}")
                        ->setParameter($aliasParameter, $condition[1]);
                }
            }
        }

        if (!empty($sortBy)) {
            foreach ($sortBy as $column => $ypeSort) {
                $qb->addOrderBy($column, $ypeSort);
            }
        }

        if (!is_null($limit)) {
            $qb = $this->paginator($qb, $page, $limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Adding simple search by fields to QueryBuilder $qb
     * @param QueryBuilder $qb
     * @param $query
     * @return QueryBuilder $qb
     */
    protected function simpleSearchByFields($qb, $query)
    {
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
        $qb->orderBy("e.$sortType", $sortOrder);
        return $qb;
    }
}
