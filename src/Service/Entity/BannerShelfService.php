<?php

namespace App\Service\Entity;

use App\Entity\VirtualPageResource\BannerShelf;
use App\Repository\VirtualPageResource\BannerShelfRepository;

/**
 * Class BannerShelfService
 * @package App\Service\Entity
 *
 * @method BannerShelfRepository getRepository()
 */
class BannerShelfService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return BannerShelf::class;
    }
}
