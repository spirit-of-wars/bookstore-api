<?php

namespace App\Service\Entity;

use App\Entity\Author;
use App\Repository\AuthorRepository;

/**
 * Class AuthorService
 * @package App\Service\Entity
 *
 * @method AuthorRepository getRepository()
 */
class AuthorService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Author::class;
    }
}
