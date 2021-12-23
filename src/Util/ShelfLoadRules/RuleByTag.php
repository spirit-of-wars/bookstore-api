<?php

namespace App\Util\ShelfLoadRules;

use App\Mif;
use App\Entity\VirtualPageResource\ProductShelf;
use App\Entity\ProductGroup\Tag;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class RuleByTag
 * @package App\Util\ShelfLoadRules
 */
class RuleByTag extends CommonShelfLoadRules
{
    /**
     * @return array|string[]
     */
    public function getJoins() : array
    {
        return [
            'pt' => 'promoTags',
            'e' => 'essence'
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
     * @throws NonUniqueResultException
     */
    public function getConditions(ProductShelf $productShelf, string $promoTagSlug) : array
    {
        $arrConditions[] = $this->getConditionsTag($productShelf->getTag());
        $promoTag = $this->promoTagService->getBySlug($promoTagSlug);
        $arrConditions[] = ['pt.id', $promoTag->getId(), '='];
        return $arrConditions;
    }
}
