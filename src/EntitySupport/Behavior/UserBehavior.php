<?php

namespace App\EntitySupport\Behavior;

use App\Constants;
use App\Mif;

trait UserBehavior
{
    public function isGuest()
    {
        return $this->getEmail() == Mif::getEnvConfig(Constants::GUEST_EMAIL);
    }
}