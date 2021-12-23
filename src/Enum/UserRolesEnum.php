<?php

namespace App\Enum;

use App\Enum\Core\Enum;

class UserRolesEnum extends Enum
{
    const SUPER_ADMIN = 'super_admin';
    const ADMIN = 'admin';
    const CLIENT = 'client';
}
