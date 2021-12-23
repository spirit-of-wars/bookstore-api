<?php

namespace App\Service\Entity;

use App\Entity\ProductEssence\Essence;
use App\Repository\ProductEssence\EssenceRepository;

/**
 * Class EssenceService
 * @package App\Service\Entity
 *
 * @method EssenceRepository getRepository()
 * @method Essence createEntity($properties)
 */
class EssenceService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Essence::class;
    }
}
