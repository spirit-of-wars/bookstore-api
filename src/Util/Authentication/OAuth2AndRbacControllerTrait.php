<?php

namespace App\Util\Authentication;

trait OAuth2AndRbacControllerTrait
{
    /**
     * @param string $action
     * @return bool
     */
    public function checkActionNeedAuthentication(string $action) : bool
    {
        if (array_key_exists($action, self::getPermissions())) {
            return true;
        }

        return false;
    }
}
