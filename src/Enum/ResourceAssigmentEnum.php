<?php

namespace App\Enum;

use App\Enum\Core\Enum;

/**
 * Class ResourceAssigmentEnum
 * @package App\Enum
 */
class ResourceAssigmentEnum extends Enum
{
    const IMAGE = 'image';
    // For products
    const COVER_IMAGE = 'coverImage';
    const SPINE_IMAGE = 'spineImage';
    //TODO pdfBookExample pdfBookExampleForSale

    // For banners
    const IMAGE_320 = 'image320';
    const IMAGE_480 = 'image480';
    const IMAGE_960 = 'image960';
    const MINI_BANNER_IMAGE = 'miniBannerImage';
}
