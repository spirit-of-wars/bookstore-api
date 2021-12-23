<?php

namespace App\Service\Entity;

use App\Entity\Product;
use App\Entity\ProductData\Data1C;
use App\Mif;
use App\Repository\ProductData\Data1CRepository;
use \Exception;

/**
 * Class Data1CService
 * @package App\Service\Entity
 *
 * @method Data1CRepository getRepository()
 * @method Data1C getNewEntityInstance($attributes = [])
 */
class Data1CService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Data1C::class;
    }

    /**
     * @param Data1C $data1C
     * @param array $attributes
     * @return Data1C
     */
    public function updateData1C(Data1C $data1C, array $attributes) : Data1C
    {
        unset($attributes["Product"]);
        $entityUpdated = $data1C->setProperties($attributes);
        $entityUpdated->save();

        return $entityUpdated;
    }

    /**
     * @param Data1C|null $previousData1C
     * @param array $attributes
     * @return Data1C
     */
    public function createNextData1C(?Data1C $previousData1C, array $attributes) : Data1C
    {
        unset($attributes['Product']);
        $isDefined = false;
        $newData1C = $this->getNewEntityInstance($attributes);

        if (!is_null($previousData1C)) {
            $previousData1C->setIsActive(false);
            $previousData1C->save();
            $product = $previousData1C->getProduct();
            $newData1C->setProduct($product);
            $isDefined = true;
        }

        $newData1C->setIsActive(true);
        $newData1C->setIsDefined($isDefined);
        $newData1C->save();
        return $newData1C;
    }

    /**
     * @param Product $product
     * @return Data1C|null
     */
    public function getData1CByProduct(Product $product) : ?Data1C
    {
        return $this->getRepository()->getByProduct($product);
    }

    /**
     * @param bool $statusFlush
     * @return string|null
     */
    public function saveData1CArray(bool $statusFlush = true) : ?string
    {
        try {
            if ($statusFlush) {
                Mif::getPersistHolder()->tryFlush();
            }
        } catch (Exception $e) {
            return $e->getCode() . " : " . $e->getMessage();
        }
        return null;
    }
}
