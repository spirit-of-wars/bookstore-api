<?php

namespace App\EntitySupport\Common;

use App\Entity\Product;
use App\Enum\ProductTypeEnum;
use App\Mif;

/**
 * Class ProductDetailFactory
 * @package App\Entity\Common
 */
class ProductDetailFactory
{
    /**
     * @param Product $product
     * @return BaseEntity|false
     */
    public static function create($product)
    {
        $entityClass = ProductTypeEnum::getEntityClassName($product->getType());
        $repo = Mif::getDoctrine()->getRepository($entityClass);
        /** @var BaseEntity $detail */
        $detail = $repo->findOneBy(['product' => $product]);
        return $detail ?? null;
    }
}
