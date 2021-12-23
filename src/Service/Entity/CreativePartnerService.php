<?php

namespace App\Service\Entity;

use App\Model\ProductModelProvider;
use App\Entity\CreativePartner;
use App\EntitySupport\Common\BaseEntity;
use App\Exception\EntityNotFoundException;
use App\Request;

/**
 * Class CreativePartnerService
 * @package App\Service\Entity
 */
class CreativePartnerService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return CreativePartner::class;
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return BaseEntity
     */
    public function createEntityFromRequest($request, $aliases = [])
    {
        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $books = $properties['books'] ?? null;
        if ($books) {
            $properties['books'] = $this->getBookEssenceIdsByProductIds($books);
        }

        return $this->createEntity($properties);
    }

    /**
     * @param Request $request
     * @param array $aliases
     * @return BaseEntity
     */
    public function updateEntityFromRequest($request, $aliases = [])
    {
        $entity = $this->getEntity($request->get('id'));
        if (!$entity) {
            throw new EntityNotFoundException('Объект не найден');
        }

        $properties = $this->extractPropertiesFromRequest($request, $aliases);
        $books = $properties['books'] ?? null;
        if ($books) {
            $properties['books'] = $this->getBookEssenceIdsByProductIds($books);
        }

        $this->updateEntity($entity, $properties);
        return $entity;
    }

    /**
     * @param array $productIds
     * @return array
     */
    private function getBookEssenceIdsByProductIds($productIds)
    {
        $result = [];
        foreach ($productIds as $id) {
            $product = ProductModelProvider::getByProductId($id);
            if ($product) {
                $result[] = $product->getEssenceDetail()->getId();
            }
        }

        return $result;
    }
}
