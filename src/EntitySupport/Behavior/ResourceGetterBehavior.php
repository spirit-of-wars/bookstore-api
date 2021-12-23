<?php

namespace App\EntitySupport\Behavior;

use App\Entity\Resource;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class ResourceGetterBehavior
 * @package App\EntitySupport\Behavior
 */
trait ResourceGetterBehavior
{
    /**
     * @param $assigment
     * @return mixed
     */
    public function getResourcesByAssigment($assigment)
    {
        $resourceRepository = Resource::getService()->getRepository();
        $relationName = $this->getSchema()->getRelation('resources')->getTargetRelationName();

        return $resourceRepository->createQueryBuilder('res')
            ->innerJoin("res.$relationName", 'e', Join::WITH, 'e = :entity')
            ->setParameter(':entity', $this)
            ->andWhere('res.assigment = :assigment')
            ->setParameter(':assigment', $assigment)
            ->getQuery()
            ->getResult();
    }
}
