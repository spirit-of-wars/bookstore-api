<?php

namespace App\Interfaces\Auth;

/**
 * Interface AuthorizationNeighborInterface
 * @package App\Interfaces\Auth
 */
interface AuthorizationNeighborInterface
{
    public function getAuthSecret(string $action) : string;
}
