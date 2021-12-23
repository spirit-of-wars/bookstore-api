<?php

namespace App\Service\Entity;

use App\Entity\ProductEssence\Book;
use App\Repository\ProductEssence\BookRepository;

/**
 * Class BookService
 * @package App\Service\Entity
 *
 * @method BookRepository getRepository()
 */
class BookService extends EntityService
{
    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return Book::class;
    }
}
