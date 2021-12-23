<?php

namespace App\EntitySupport\Behavior;

use App\EntitySupport\Common\BaseEntity;
use App\Enum\VirtualPageResourceTypeEnum;
use App\Mif;

/**
 * Trait VirtualPageBehavior
 * @package App\EntitySupport\Behavior
 */
trait VirtualPageBehavior
{
    /**
     * @param $resourceType
     * @param $idResource
     * @return BaseEntity|null
     */
    public function loadResource($idResource, $resourceType)
    {
        $resourceEntityClass = VirtualPageResourceTypeEnum::getEntityClassName($resourceType);
        if (!$resourceEntityClass) {
            return null;
        }

        $repo = Mif::getDoctrine()->getRepository($resourceEntityClass);
        /** @var BaseEntity $resourceEntity */
        $resourceEntity = $repo->find($idResource);
        return $resourceEntity ?? null;
    }
}
