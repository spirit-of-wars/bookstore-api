<?php

namespace App\Service\Entity;

use App\Entity\Resource;
use App\EntitySupport\Behavior\ResourceGetterBehavior;
use App\EntitySupport\Common\BaseEntity;
use App\Exception\BadRequestException;
use App\Mif;
use App\Repository\ResourceRepository;
use Exception;

/**
 * Class ResourceService
 * @package App\Service\Entity
 *
 * @method ResourceRepository getRepository()
 */
class ResourceService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Resource::class;
    }

    /**
     * @param ResourceGetterBehavior $entity
     * @param array $resourceAssignments
     * @param array $properties
     * @return array
     * @throws BadRequestException
     * @throws Exception
     */
    public function defineResourcesByAssignments($entity, $resourceAssignments, &$properties)
    {
        $allResources = [];
        Mif::getPersistHolder()->hold();

        foreach ($resourceAssignments as $resourceAssignment => $resourceRelationName) {
            $resources = [];
            if (!array_key_exists($resourceAssignment, $properties)) {
                /** @var Resource[] $currentResources */
                $currentResources = $entity->getResourcesByAssigment($resourceAssignment);
                foreach ($currentResources as $currentResource) {
                    $resources[] = $currentResource->getId();
                }
                if (!empty($resources)) {
                    if (!array_key_exists($resourceRelationName, $allResources)) {
                        $allResources[$resourceRelationName] = [];
                    }

                    $allResources[$resourceRelationName] = array_merge(
                        $allResources[$resourceRelationName],
                        $resources
                    );
                }
                continue;
            }

            $resourceIds = $properties[$resourceAssignment] ?? null;
            if (is_null($resourceIds)) {
                continue;
            }

            unset($properties[$resourceAssignment]);
            $resourceIds = (array)$resourceIds;

            foreach ($resourceIds as $resourceId) {
                /** @var Resource $resource */
                $resource = Mif::getServiceProvider()->FileService->getEntity($resourceId);
                if (!$resource) {
                    Mif::getPersistHolder()->drop();
                    throw new BadRequestException('Ресурс не найден, id = ' . $resourceId);
                }

                $assigment = $resource->getAssigment();
                if ($assigment && $assigment != $resourceAssignment
                    && !$this->checkAssignmentRelations($resource, $entity)) {

                    throw new Exception('Resource with id = ' . $resource->getId() . ' was already tied');
                }

                $resource->setAssigment($resourceAssignment);
                $resource->save();
                $resources[] = $resourceId;
            }
            if (!empty($resources)) {
                if (!array_key_exists($resourceRelationName, $allResources)) {
                    $allResources[$resourceRelationName] = [];
                }

                $allResources[$resourceRelationName] = array_merge(
                    $allResources[$resourceRelationName],
                    $resources
                );
            }
        }

        Mif::getPersistHolder()->commit();

        $properties = array_merge($properties, $allResources);

        return $properties;
    }

    /**
     * @param Resource $resource
     * @param ResourceGetterBehavior $entity
     * @return bool
     */
    private function checkAssignmentRelations($resource, $entity)
    {
        $relations = $resource->getSchema()->getRelations();

        foreach ($relations as $relation) {
            $targetEntityClass = $relation->getTargetEntityClass();
            if (!$entity instanceof $targetEntityClass && $resource->getRelatedEntity($targetEntityClass)) {

                return false;
            }
        }

        return true;
    }
}
