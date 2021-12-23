<?php

namespace App\Service\Entity;

use App\Entity\ProductGroup\Category;
use App\Repository\ProductGroup\CategoryRepository;

/**
 * Class CategoryService
 * @package App\Service\Entity
 *
 * @method CategoryRepository getRepository()
 */
class CategoryService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Category::class;
    }
}
