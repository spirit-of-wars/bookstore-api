<?php

namespace App\Util\ShelfLoadRules;

use App\Entity\Product;
use App\Entity\ProductGroup\Tag;
use App\Model\ProductModel;

class ShelfLoadRuleHelper
{
    private static array $arrPromoTag = [
        'novelty' => [
                'p.startSaleDate' => 'ASC',
        ],
        'bestsellers' => [],
        'promotions' => [],
        'soon' => [
                'p.startSaleDate' => 'ASC',
        ],
    ];

    public static function getProductSortRules(?string $slug = null) : array
    {
        if (!is_null($slug)) {
            return [$slug => self::$arrPromoTag[$slug]];
        }

        return self::$arrPromoTag;
    }

    /**
     * @param array $collectionProduct
     * @return array
     */
    public static function getArrayProducts(array $collectionProduct) : array
    {
        $productsArray = [];
        /** @var Product $product */
        foreach ($collectionProduct as $product) {

            //TODO требует актуализации
            $productModel = new ProductModel($product);

            $productData = $productModel->toArray();
            /** @var Tag $tag */
            foreach ($product->getEssence()->getTags() as $tag) {
                $productData['tags'][$tag->getSlug()] = $tag->getProperties(
                    null,
                    ['essences', 'productShelves', 'createdAt', 'updatedAt']
                );
            }

            $productsArray[] = $productData;
        }

        return $productsArray;
    }

    /**
     * @param array $bestsellers
     * @return array
     */
    public static function sortBestsellers(array $bestsellers) : array
    {
        $bookIds = [];
        $result = [];
        foreach ($bestsellers as $key => $val)
        {
            if (is_array($val) && !array_key_exists('id', $val)) {
                $result[$key] = self::sortBestsellers($val);
                continue;
            }

            $bookIds[$key] = $val['id'];
        }

        if (empty($bookIds)) {
            return $result;
        }

        $sortForBestsellersBooks = self::getSellingBooks($bookIds);
        foreach ($sortForBestsellersBooks as $key => $val) {
            $result[] = $bestsellers[$key];
        }

        return $result;
    }

    /**
     * @param array $bookIds
     * @return array
     */
    private static function getSellingBooks(array $bookIds) : array
    {
        if (empty($bookIds)) {
            return [];
        }

        $result = [];
        //TODO сделать запрос для получения списка бестселлеров по прадажам
        foreach ($bookIds as $key => $bookId) {
            $result[$key] = rand(0, 99);
        }

        arsort($result);
        return $result;
    }
}
