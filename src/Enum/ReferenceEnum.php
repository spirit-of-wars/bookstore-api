<?php

namespace App\Enum;

use App\Enum\Core\Enum;
use App\Interfaces\Enum\ReferenceInterface;

class ReferenceEnum extends Enum
{
    const PRODUCT_TYPE = 'productType';
    const RESOURCE_TYPE = 'resourceType';
    const PRODUCT_LIFE_CYCLE_STATUS = 'productLifeCycleStatus';

    /**
     * @param array $references
     * @throws \Exception
     */
    protected static function checkReference(array $references)
    {
        foreach ($references as $reference) {
            if(!in_array($reference, self::getList())) {
                throw new \Exception('Invalid reference parameter passed: ' . $reference);
            }
        }
    }

    /**
     * @return ReferenceInterface[]
     */
    public static function getEnumClassList()
    {
        return [
            self::PRODUCT_TYPE => ProductTypeEnum::class,
            self::RESOURCE_TYPE => ResourceTypeEnum::class,
            self::PRODUCT_LIFE_CYCLE_STATUS => ProductLifeCycleStatusEnum::class
        ];
    }

    /**
     * @param array $filter
     * @return ReferenceInterface[]|array
     * @throws \Exception
     */
    public static function getEnumClassListByFilters(array $filter = [])
    {
        $list = self::getEnumClassList();
        if ($filter) {
            self::checkReference($filter);
            $list = array_intersect_key($list, array_flip($filter));
        }

        return $list;
    }
}
