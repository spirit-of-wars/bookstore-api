<?php

namespace App\Service;

use App\Enum\ReferenceEnum;
use App\Interfaces\Enum\ReferenceInterface;
use Exception;

/**
 * Class ReferenceService
 * @package App\Service
 *
 */
class ReferenceService extends Service
{

    /**
     * @param array $filter
     * @return ReferenceInterface[]|array
     * @throws Exception
     */
    public function getReferencesByFilter(array $filter = [])
    {
        $references = [];
        $referenceEnums = ReferenceEnum::getEnumClassListByFilters($filter);
        foreach ($referenceEnums as $key => $referenceEnumClass) {
            $references[$key] = $referenceEnumClass::getReferences();
        }

        return $references;
    }
}
