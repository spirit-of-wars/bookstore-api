<?php

namespace App\Util\ShelfLoadRules;

use App\Entity\ProductGroup\Category;
use App\Entity\VirtualPageResource\ProductShelf;
use App\Entity\ProductGroup\Tag;
use Doctrine\ORM\NonUniqueResultException;

class RuleByPromoTagCategoryTag extends CommonShelfLoadRules
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

    private function getConditionsCategory(Category $category) : array
    {
        return ['e.category', $category->getId(), '='];
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
     * @throws NonUniqueResultException
     */
    public function getConditions(ProductShelf $productShelf, string $promoTagSlug) : array
    {
        $arrConditions[] = $this->getConditionsTag($productShelf->getTag());
        $arrConditions[] = $this->getConditionsCategory($productShelf->getCategory());
        $promoTag = $this->promoTagService->getBySlug($promoTagSlug);
        $arrConditions[] = ['pt.id', $promoTag->getId(), '='];
        return $arrConditions;
    }
}