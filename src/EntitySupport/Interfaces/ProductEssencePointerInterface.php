<?php

namespace App\EntitySupport\Interfaces;

use App\EntitySupport\Common\BaseEntity;

/**
 * Interface ProductEssencePointerInterface
 * @package App\EntitySupport\Interfaces
 *
 * TODO - фиктивный интерфейс. Напрямую навешать на модель нельзя, т.к. требует реализовать метод
 * __call его не устраивает. Решено пока через этот интерфейс реализовать документирование комментарием
 *
 * Interface methods are implemented is entities:
 * - ProductType\AudioBook
 * - ProductType\EBook
 * - ProductType\PaperBook
 */
interface ProductEssencePointerInterface
{
    /**
     * @param BaseEntity $essence
     */
    public function setEssence($essence);

    /**
     * @return BaseEntity
     */
    public function getEssence();
}
