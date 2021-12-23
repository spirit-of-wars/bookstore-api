<?php

namespace App\EntitySupport\Behavior;

use App\Util\ShelfLoadRules\RuleByCategory;
use App\Util\ShelfLoadRules\RuleByPromoTag;
use App\Util\ShelfLoadRules\RuleByTag;
use App\Util\ShelfLoadRules\RuleByTagPromoTag;
use App\Util\ShelfLoadRules\RuleByTagCategory;
use App\Util\ShelfLoadRules\RuleByPromoTagCategory;
use App\Util\ShelfLoadRules\RuleByPromoTagCategoryTag;
use App\Util\ShelfLoadRules\CommonShelfLoadRules;

trait ShelfLoadBehavior
{
    public function getShelfLoadRules() : ?CommonShelfLoadRules
    {
        if (!is_null($this->getPromoTag()) && !is_null($this->getCategory()) && !is_null($this->getTag())) {
            return new RuleByPromoTagCategoryTag();
        }

        if (!is_null($this->getPromoTag()) && !is_null($this->getCategory())) {
            return new RuleByPromoTagCategory();
        }

        if (!is_null($this->getPromoTag()) && !is_null($this->getTag())) {
            return new RuleByTagPromoTag();
        }

        if (!is_null($this->getTag()) && !is_null($this->getCategory())) {
            return new RuleByTagCategory();
        }

        if (!is_null($this->getPromoTag())) {
            return new RuleByPromoTag();
        }

        if (!is_null($this->getTag())) {
            return new RuleByTag();
        }

        if (!is_null($this->getCategory())) {
            return new RuleByCategory();
        }

        return null;
    }
}
