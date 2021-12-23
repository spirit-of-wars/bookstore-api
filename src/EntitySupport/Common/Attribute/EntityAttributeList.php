<?php

namespace App\EntitySupport\Common\Attribute;

use App\EntitySupport\Common\EntityConstants;

/**
 * Class EntityAttributeList
 * @package App\Entity\Common\Attribute
 */
class EntityAttributeList extends EntityAttribute
{
    /**
     * @return string|null
     */
    public function getDocumentationComment()
    {
        return 'Тип: линейный массив';
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
        $argument = $arguments[0] ?? [];
        if (is_array($argument)) {
            foreach ($argument as $value) {
                $oldValue[] = $value;
            }
        } else {
            $oldValue[] = $argument;
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
        $argument = $arguments[0] ?? [];
        if (is_array($argument)) {
            foreach ($argument as $value) {
                $index = array_search($value, $oldValue);
                if ($index !== false) {
                    unset($oldValue[$index]);
                    $oldValue = array_values($oldValue);
                }
            }
        } else {
            $index = array_search($argument, $oldValue);
            if ($index !== false) {
                unset($oldValue[$index]);
                $oldValue = array_values($oldValue);
            }
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
