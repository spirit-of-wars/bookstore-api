<?php

namespace App\Enum;

use App\Enum\Core\Enum;
use App\Entity\ProductEssence\Book;

/**
 * Class ProductEssenceTypeEnum
 * @package App\Enum
 */
class ProductEssenceTypeEnum extends Enum
{
    const BOOK = Book::class;

    /**
     * @param string $type
     * @return string
     */
    public static function getEntityClassName($type)
    {
        $className = null;
        switch ($type) {
            case ProductTypeEnum::PAPER_BOOK:
            case ProductTypeEnum::E_BOOK:
            case ProductTypeEnum::AUDIO_BOOK:
                $className = self::BOOK;
        }

        return $className;
    }
}
