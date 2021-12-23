<?php

namespace App\Interfaces\Auth;

/**
 * Interface AuthorizationRbacInterface
 * @package App\Interfaces\Auth
 */
interface AuthorizationRbacInterface
{
    public function getRightForAction(string $action) : array;

    public static function getPermissions() : array;
}
