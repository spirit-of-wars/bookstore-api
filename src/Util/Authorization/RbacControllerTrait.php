<?php

namespace App\Util\Authorization;

trait RbacControllerTrait
{
    /**
     * @param string $action
     * @return array|string[]|null
     */
    public function getRightForAction(string $action) : array
    {
        $arrRightMap = self::getPermissions();
        if (array_key_exists($action, $arrRightMap)) {
            return $arrRightMap[$action];
        }

        return [];
    }

    /**
     * @return array
     */
    public static function getPermissions() : array
    {
        return [];
    }
}
