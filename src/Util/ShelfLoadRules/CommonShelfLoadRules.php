<?php

namespace App\Util\ShelfLoadRules;

use App\Entity\VirtualPageResource\ProductShelf;
use App\Entity\ProductGroup\PromoTag;
use App\Mif;
use App\Service\Entity\Product\CommonService as ProductCommonService;
use App\Service\Entity\PromoTagService;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class CommonShelfLoadRules
 * @package App\Util\ShelfLoadRules
 */
abstract class CommonShelfLoadRules
{
    const CATEGORY_LIMIT_PRODUCT = 4;

    protected ProductCommonService $productService;
    protected PromoTagService $promoTagService;

    /**
     * CommonShelfLoadRules constructor.
     */
    public function __construct()
    {
        $this->productService = Mif::getServiceProvider()->ProductCommonService;
        $this->promoTagService = Mif::getServiceProvider()->PromoTagService;
    }

    /**
     * @param ProductShelf $productShelf
     * @param int|null $page
     * @param int|null $limit
     * @param string|null $alias
     * @return array
     * @throws NonUniqueResultException
     */
    public function getProductsForShelf(
        ProductShelf $productShelf,
        ?int $page = null,
        ?int $limit = null,
        ?string $alias = 'p'
    ) : array
    {
        $shelf['shelf'] = $productShelf->getAttributes(null, ['createdAt', 'updatedAt']);

        $joins = $this->getJoins();
        $sortBy = $this->getSortBy($productShelf->getPromoTag());

        if ($this instanceof RuleByCategory) {
            if (is_null($limit)) {
                $limit = self::CATEGORY_LIMIT_PRODUCT;
            }

            if (is_null($page)) {
                $page = 1;
            }
        }

        foreach ($sortBy as $key => $val) {
            $conditions = $this->getConditions($productShelf, $key);

            if ($key === 'soon') {
                $conditions[] = [
                    'p.startSaleDate',
                    date('Y-m-d H:i:s', strtotime('+2 month')),
                    '<'
                ];
            }

            $collectionProducts = $this->productService->getEntityByAttributesAndSort(
                $alias,
                $joins,
                $conditions,
                $val,
                $page,
                $limit
            );

            $shelf['products'][$key] = ShelfLoadRuleHelper::getArrayProducts($collectionProducts);
        }

        if (isset($shelf['products']['bestsellers'])) {
            $shelf['products']['bestsellers'] = ShelfLoadRuleHelper::sortBestsellers($shelf['products']['bestsellers']);
        }

        return $shelf;
    }

    /**
     * @return array
     */
    abstract protected function getJoins() : array;

    /**
     * @param ProductShelf $productShelf
     * @param string $promoTagSlug
     * @return array
     */
    abstract protected function getConditions(ProductShelf $productShelf, string $promoTagSlug) : array;

    /**
     * @param PromoTag|null $promoTag
     * @return array
     */
    private function getSortBy(?PromoTag $promoTag = null): array
    {
        $slug = null;
        if (!is_null($promoTag)) {
            $slug = $promoTag->getSlug();
        }

        return ShelfLoadRuleHelper::getProductSortRules($slug);
    }
}
