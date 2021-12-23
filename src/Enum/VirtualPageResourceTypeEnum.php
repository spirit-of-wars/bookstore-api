<?php

namespace App\Enum;

use App\Enum\Core\Enum;
use App\Entity\Banner\Banner;
use App\Entity\Banner\BannerShelf;
use App\Entity\Banner\Factoid;
use App\Entity\ProductGroup\ProductShelf;

/**
 * Class VirtualPageResourceTypeEnum
 * @package App\Enum
 */
class VirtualPageResourceTypeEnum extends Enum
{
    const BANNER_SHELF = 'banner_shelf';
    const BANNER = 'banner';
    const FACTOID = 'factoid';
    const PRODUCT_SHELF = 'product_shelf';

    /**
     * @param string $type
     * @return string
     */
    public static function getEntityClassName($type)
    {
        $list = self::getEntityClassNameList();
        return $list[$type] ?? null;
    }

    /**
     * @return array
     */
    public static function getEntityClassNameList()
    {
        return [
            self::BANNER_SHELF => BannerShelf::class,
            self::BANNER => Banner::class,
            self::FACTOID => Factoid::class,
            self::PRODUCT_SHELF => ProductShelf::class,
        ];
    }
}
