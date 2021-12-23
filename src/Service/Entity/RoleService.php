<?php

namespace App\Service\Entity;

use App\Entity\Auth\Role;

class RoleService extends EntityService
{

    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Role::class;
    }

    /**
     * @param string $name
     * @return Role|null
     */
    public function getByName(string $name) : ?Role
    {
        return $this->getRepository()->getByName($name);
    }
}
