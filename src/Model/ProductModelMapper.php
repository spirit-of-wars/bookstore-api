<?php

namespace App\Model;

use App\Entity\ProductEssence\Book;
use App\Entity\ProductType\AudioBook;
use App\Entity\ProductType\EBook;
use App\Entity\ProductType\PaperBook;

/**
 * Class ProductModelMapper
 * @package App\Model
 */
class ProductModelMapper
{
    const ESSENCE_DEPENDENCIES_MAP = [
        Book::class => [
            PaperBook::class,
            EBook::class,
            AudioBook::class,
        ],
    ];

    /**
     * @param string $productTypeClass
     * @return string|null
     */
    public static function defineEssenceDependency($productTypeClass)
    {
        foreach (self::ESSENCE_DEPENDENCIES_MAP as $essenceClass => $dependencies) {
            if (in_array($productTypeClass, $dependencies)) {
                return $essenceClass;
            }
        }

        return null;
    }
}
