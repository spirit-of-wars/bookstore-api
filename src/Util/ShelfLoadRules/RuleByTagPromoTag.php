<?php

namespace App\Util\ShelfLoadRules;

use App\Entity\VirtualPageResource\ProductShelf;
use App\Entity\ProductGroup\Tag;

class RuleByTagPromoTag extends CommonShelfLoadRules
{
    /**
     * @return array|string[]
     */
    public function getJoins() : array
    {
        return [
            'e' => 'essence',
            'pt' => 'promoTags',
        ];
    }

    /**
     * @param Tag $tag
     * @return array
     */
    private function getConditionsTag(Tag $tag): array
    {
        return ['e.tags', $tag, 'MEMBER OF'];
    }

    /**
     * @param ProductShelf $productShelf
     * @param string $promoTagSlug
     * @return array
     */
    public function getConditions(ProductShelf $productShelf, string $promoTagSlug) : array
    {
        $arrConditions[] = $this->getConditionsTag($productShelf->getTag());
        $promoTag = $productShelf->getPromoTag();
        $arrConditions[] = ['pt.id', $promoTag->getId(), '='];
        return $arrConditions;
    }
}