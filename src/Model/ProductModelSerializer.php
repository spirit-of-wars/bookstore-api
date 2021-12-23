<?php

namespace App\Model;

use App\Mif;

/**
 * Class ProductModelSerializer
 * @package App\Model
 */
class ProductModelSerializer
{
    /**
     * @param ProductModel $productModel
     * @return array
     */
    public function serialize($productModel)
    {
        $attributes = $productModel->getAttributes();
        $serializer = Mif::getServiceProvider()->EntitySerializer;

        $result = $serializer->serializeAttributes($attributes);
        $data1C = $productModel->getData1C();

        $result['essenceId'] = $productModel->getEssence()->getId();

        if ($data1C) {
            $result['data1c'] = [
                'id1c' => $data1C->getId1c(),
                'isbn' => $data1C->getIsbn(),
                'rrPrice' => $data1C->getRrPrice(),
            ];
        } else {
            $result['data1c'] = null;
        }

        return $result;
    }
}
