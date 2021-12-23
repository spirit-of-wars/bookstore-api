<?php

namespace App\Service\Entity;

use App\Entity\VirtualPageResource\Factoid;
use App\Repository\VirtualPageResource\FactoidRepository;
use App\Enum\ResourceAssigmentEnum;
use App\Request;
use App\Mif;
use App\Exception\EntityNotFoundException;

/**
 * Class FactoidService
 * @package App\Service\Entity
 *
 * @method FactoidRepository getRepository()
 */
class FactoidService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
       return Factoid::class;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return Factoid
     * @throws EntityNotFoundException
     */
    public function updateEntityFromRequest($request, $aliases = [])
    {
        /** @var Factoid $entity */
        $entity = $this->getEntity($request->get('id'));

        if (!$entity) {
            throw new EntityNotFoundException('Объект не найден');
        }

        $resourceService = Mif::getServiceProvider()->ResourceService;
        $properties = $request->all();
        $resourceService->defineResourcesByAssignments(
            $entity,
            [
                ResourceAssigmentEnum::IMAGE => 'image',
            ],
            $properties
        );

        $this->updateEntity($entity, $properties);

        return $entity;
    }
}
