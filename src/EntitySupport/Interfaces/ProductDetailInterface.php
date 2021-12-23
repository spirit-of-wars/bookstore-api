<?php

namespace App\EntitySupport\Interfaces;

use App\Entity\Product;

/**
 * Interface ProductDetailInterface
 * @package App\EntitySupport\Interfaces
 *
 * TODO - фиктивный интерфейс. Напрямую навешать на модель нельзя, т.к. требует реализовать метод
 * __call его не устраивает. Решено пока через этот интерфейс реализовать документирование комментарием
 *
 * Interface methods are implemented is entities:
 * - ProductType\AudioBook
 * - ProductType\Badge
 * - ProductType\Bag
 * - ProductType\Bookmark
 * - ProductType\Certificate
 * - ProductType\Cloth
 * - ProductType\Course
 * - ProductType\EBook
 * - ProductType\Game
 * - ProductType\Kit
 * - ProductType\KitItem
 * - ProductType\Notepad
 * - ProductType\PaperBook
 * - ProductType\Postcard
 * - ProductType\Poster
 * - ProductType\Sticker
 */
interface ProductDetailInterface
{
    /**
     * @param Product $product
     */
    public function setProduct($product);

    /**
     * @return Product
     */
    public function getProduct();
}
