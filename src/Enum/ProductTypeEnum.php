<?php

namespace App\Enum;

use App\Enum\Core\Enum;
use App\Entity\ProductType\Badge;
use App\Entity\ProductType\Bag;
use App\Entity\ProductType\Bookmark;
use App\Entity\ProductType\Certificate;
use App\Entity\ProductType\Cloth;
use App\Entity\ProductType\Course;
use App\Entity\ProductType\Game;
use App\Entity\ProductType\Kit;
use App\Entity\ProductType\KitItem;
use App\Entity\ProductType\Notepad;
use App\Entity\ProductType\PaperBook;
use App\Entity\ProductType\EBook;
use App\Entity\ProductType\AudioBook;
use App\Entity\ProductType\Postcard;
use App\Entity\ProductType\Poster;
use App\Entity\ProductType\Sticker;
use App\Interfaces\Enum\ReferenceInterface;

/**
 * Class ProductTypeEnum
 * @package App\Enum
 */
class ProductTypeEnum extends Enum implements ReferenceInterface
{
    const PAPER_BOOK = 'paper_book';
    const E_BOOK = 'e_book';
    const AUDIO_BOOK = 'audio_book';
    const COURSE = 'course';
    const GAME = 'game';
    const NOTEPAD = 'notepad';
    const BOOKMARK = 'bookmark';
    const POSTER = 'poster';
    const POSTCARD = 'postcard';
    const STICKER = 'sticker';
    const CLOTH = 'cloth';
    const BADGE = 'badge';
    const BAG = 'bag';
    const KIT = 'kit';
    const KIT_ITEM = 'kit_item';
    const CERTIFICATE = 'certificate';

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
            self::PAPER_BOOK => PaperBook::class,
            self::E_BOOK => EBook::class,
            self::AUDIO_BOOK => AudioBook::class,
            self::COURSE => Course::class,
            self::GAME => Game::class,
            self::NOTEPAD => Notepad::class,
            self::BOOKMARK => Bookmark::class,
            self::POSTER => Poster::class,
            self::POSTCARD => Postcard::class,
            self::STICKER => Sticker::class,
            self::CLOTH => Cloth::class,
            self::KIT => Kit::class,
            self::KIT_ITEM => KitItem::class,
            self::CERTIFICATE => Certificate::class,
            self::BADGE => Badge::class,
            self::BAG => Bag::class,
        ];
    }

    /**
     * @return array
     */
    public static function getReferences()
    {
        return [
            self::PAPER_BOOK => '???????????????? ??????????',
            self::E_BOOK => '?????????????????????? ??????????',
            self::AUDIO_BOOK => '?????????? ??????????',
            self::COURSE => '????????',
            self::GAME => '????????',
            self::NOTEPAD => '??????????????',
            self::BOOKMARK => '????????????????',
            self::POSTER => '????????????',
            self::POSTCARD => '????????????????',
            self::STICKER => '????????????',
            self::CLOTH => '????????????',
            self::KIT => '????????????????',
            self::BADGE => '????????????',
            self::BAG => '??????????',
            self::KIT_ITEM => '?????????????? ??????????????????',
            self::CERTIFICATE => '????????????????????',
        ];
    }
}
