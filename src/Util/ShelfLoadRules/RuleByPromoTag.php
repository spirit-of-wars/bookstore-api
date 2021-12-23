<?php

namespace App\Util\ShelfLoadRules;

use App\Entity\VirtualPageResource\ProductShelf;

/**
 * Class RuleByPromoTag
 * @package App\Util\ShelfLoadRules
 */
class RuleByPromoTag extends CommonShelfLoadRules
{
    /**
     * @return array|string[]
     */
    public function getJoins() : array
    {
        return [
            'pt' => 'promoTags'
        ];
    }

    /**
     * @param ProductShelf $productShelf
     * @param string $promoTagSlug
     * @return array
     */
    public function getConditions(ProductShelf $productShelf, string $promoTagSlug) : array
    {
        $promoTag = $productShelf->getPromoTag();
        return [
            [
                'pt.id', $promoTag->getId(), '=',
            ]
        ];
    }
}
