<?php

namespace App\EntitySupport\Common;

use App\Entity\Auth\AccessToken;
use App\Entity\Auth\ConfirmLink;
use App\Entity\Auth\RefreshToken;
use App\Entity\Auth\Role;
use App\Entity\Auth\SocNetworkUserData;
use App\Entity\Author;
use App\Entity\CreativePartner;
use App\Entity\Product;
use App\Entity\ProductData\Data1C;
use App\Entity\ProductEssence\Book;
use App\Entity\ProductEssence\Essence;
use App\Entity\ProductGroup\Category;
use App\Entity\ProductGroup\PromoTag;
use App\Entity\ProductGroup\Series;
use App\Entity\ProductGroup\Tag;
use App\Entity\Resource;
use App\Entity\User;
use App\Entity\VirtualPage;
use App\Entity\VirtualPageResource\Banner;
use App\Entity\VirtualPageResource\BannerShelf;
use App\Entity\VirtualPageResource\Factoid;
use App\Entity\VirtualPageResource\ProductShelf;

/**
 * Class EntityServiceMap
 * @package App\EntitySupport\Common
 */
class EntityServiceMap
{
    const MAP = [
        Product::class => 'ProductCommonService',
        AccessToken::class => 'AccessTokenService',
        Author::class => 'AuthorService',
        Banner::class => 'BannerService',
        BannerShelf::class => 'BannerShelfService',
        Book::class => 'BookService',
        Category::class => 'CategoryService',
        ConfirmLink::class => 'ConfirmLinkService',
        CreativePartner::class => 'CreativePartnerService',
        Data1C::class => 'Data1CService',
        Essence::class => 'EssenceService',
        Factoid::class => 'FactoidService',
        ProductShelf::class => 'ProductShelfService',
        PromoTag::class => 'PromoTagService',
        RefreshToken::class => 'RefreshTokenService',
        Role::class => 'RoleService',
        Series::class => 'SeriesService',
        SocNetworkUserData::class => 'SocNetworkUserDataService',
        Tag::class => 'TagService',
        User::class => 'UserService',
        VirtualPage::class => 'VirtualPageService',
        Resource::class => 'ResourceService',
    ];
}
