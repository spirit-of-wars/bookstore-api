<?php

namespace App\MifTools;

use App\Entity\User;

class UserManager
{
    private User $user;

    /**
     * @return User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user) : void
    {
        $this->user = $user;
    }
}
