<?php

namespace App\Interfaces\Auth;

interface AuthenticationOAuth2Interface
{
    public function checkActionNeedAuthentication(string $action) : bool;
}
