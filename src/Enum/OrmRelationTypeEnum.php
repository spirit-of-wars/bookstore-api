<?php

namespace App\Enum;

use App\Enum\Core\Enum;

/**
 * Class OrmRelationTypeEnum
 * @package App\Enum
 */
class OrmRelationTypeEnum extends Enum
{
    const MANY_TO_MANY = 'manyToMany';
    const ONE_TO_MANY = 'oneToMany';
    const MANY_TO_ONE = 'manyToOne';
    const ONE_TO_ONE = 'oneToOne';

    /**
     * @param string $type
     * @return string|false
     */
    public static function getContrType($type)
    {
        switch ($type) {
            case OrmRelationTypeEnum::ONE_TO_ONE:
                return OrmRelationTypeEnum::ONE_TO_ONE;
            case OrmRelationTypeEnum::ONE_TO_MANY:
                return OrmRelationTypeEnum::MANY_TO_ONE;
            case OrmRelationTypeEnum::MANY_TO_ONE:
                return OrmRelationTypeEnum::ONE_TO_MANY;
            case OrmRelationTypeEnum::MANY_TO_MANY:
                return OrmRelationTypeEnum::MANY_TO_MANY;
        }
        return false;
    }
}
