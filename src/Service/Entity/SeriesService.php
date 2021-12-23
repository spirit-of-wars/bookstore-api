<?php

namespace App\Service\Entity;

use App\Model\ProductModelProvider;
use App\Entity\ProductGroup\Series;
use App\Repository\ProductGroup\SeriesRepository;

/**
 * Class SeriesService
 * @package App\Service\Entity
 *
 * @method SeriesRepository getRepository()
 */
class SeriesService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Series::class;
    }

    /**
     * @param array $properties
     */
    protected function preprocessProperties(&$properties)
    {
        $ids = $properties['products'] ?? null;
        if ($ids) {
            unset($properties['products']);
            $properties['essences'] = $this->getProductEssenceIdsByProductIds($ids);
        }
    }

    /**
     * @param array $productIds
     * @return array
     */
    private function getProductEssenceIdsByProductIds($productIds)
    {
        $result = [];
        foreach ($productIds as $id) {
            $product = ProductModelProvider::getByProductId($id);
            if ($product) {
                $result[] = $product->getEssence()->getId();
            }
        }

        return $result;
    }
}
