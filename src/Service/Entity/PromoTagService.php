<?php

namespace App\Service\Entity;

use App\Entity\ProductGroup\PromoTag;
use App\Repository\ProductGroup\PromoTagRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class PromoTagService
 * @package App\Service\Entity
 *
 * @method PromoTagRepository getRepository()
 */
class PromoTagService extends EntityService
{
    /**
     * @return string
     */
    public function getEntityClassName()
    {
        return PromoTag::class;
    }

    /**
     * @param string $slug
     * @return PromoTag|null
     * @throws NonUniqueResultException
     */
    public function getBySlug(string $slug) : ?PromoTag
    {
        return $this->getRepository()->getBySlug($slug);
    }
}
