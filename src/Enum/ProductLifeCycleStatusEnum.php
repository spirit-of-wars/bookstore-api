<?php

namespace App\Enum;

use App\Enum\Core\Enum;
use App\Interfaces\Enum\ReferenceInterface;

/**
 * Class ProductLifeCycleStatusEnum
 * @package App\Enum
 */
class ProductLifeCycleStatusEnum extends Enum implements ReferenceInterface
{
    const IS_CREATED = 'created';
    const IS_AVAILABLE_FOR_SHOW = 'availableForShow';
    const IS_AVAILABLE_FOR_SELL = 'availableForSell';

    public static function getReferences()
    {
        return [
            self::IS_CREATED => 'Создан',
            self::IS_AVAILABLE_FOR_SHOW => 'Опубликован на сайте',
            self::IS_AVAILABLE_FOR_SELL => 'Продается',
        ];
    }
}
