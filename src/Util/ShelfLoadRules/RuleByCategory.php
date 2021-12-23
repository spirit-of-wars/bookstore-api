<?php

namespace App\Util\ShelfLoadRules;

use App\Entity\ProductGroup\Category;
use App\Entity\VirtualPageResource\ProductShelf;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class RuleByCategory
 * @package App\Util\ShelfLoadRules
 */
class RuleByCategory extends CommonShelfLoadRules
{
    /**
     * @return array|string[]
     */
    public function getJoins() : array
    {
        return [
            'pt' => 'promoTags',
            'e' => 'essence',
        ];
    }

    private function getConditionsCategory(Category $category) : array
    {
        return ['e.category', $category->getId(), '='];
    }

    /**
     * @param ProductShelf $productShelf
     * @param string $promoTagSlug
     * @return array
     * @throws NonUniqueResultException
     */
    protected function getConditions(ProductShelf $productShelf, string $promoTagSlug) : array
    {
        $arrConditions[] = $this->getConditionsCategory($productShelf->getCategory());
        $promoTag = $this->promoTagService->getBySlug($promoTagSlug);
        $arrConditions[] = ['pt.id', $promoTag->getId(), '='];
        return $arrConditions;
    }
}
