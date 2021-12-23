<?php

namespace App\EntitySupport\Common\Attribute;

use App\EntitySupport\Common\EntityConstants;

/**
 * Class EntityAttributeDictionary
 * @package App\Entity\Common\Attribute
 */
class EntityAttributeDictionary extends EntityAttribute
{
    /**
     * @return string|null
     */
    public function getDocumentationComment()
    {
        return 'Тип: хэш-таблица';
    }

    /**
     * @param array $value
     * @param string $key
     * @return mixed
     */
    public function onActionGetItem($value, $key)
    {
        return $value[$key] ?? null;
    }

    /**
     * @param array $oldValue
     * @param array $arguments
     * @return array
     */
    public function onActionAdd($oldValue, $arguments)
    {
        $map = [];
        $count = count($arguments);
        if ($count == 2) {
            $map = [$arguments[0] => $arguments[1]];
        } elseif ($count == 1 && is_array($arguments[0])) {
            $map = $arguments[0];
        }
        foreach ($map as $key => $value) {
            $oldValue[$key] = $value;
        }

        return $oldValue;
    }

    /**
     * @param array $oldValue
     * @param array $arguments
     * @return array
     */
    public function onActionRemove($oldValue, $arguments)
    {
        $list = ((array)$arguments[0]) ?? [];
        foreach ($list as $key) {
            unset($oldValue[$key]);
        }

        return $oldValue;
    }

    /**
     * @return array
     */
    public function getAllowedActions()
    {
        return array_merge(parent::getAllowedActions(), [
            EntityConstants::ACTION_GET_ITEM,
            EntityConstants::ACTION_ADD,
            EntityConstants::ACTION_REMOVE,
        ]);
    }
}
