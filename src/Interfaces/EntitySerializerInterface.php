<?php

namespace App\Interfaces;

use App\EntitySupport\Common\BaseEntity;
use Doctrine\Common\Collections\Collection;

/**
 * Interface EntitySerializerInterface
 * @package App\Interfaces
 */
interface EntitySerializerInterface
{
    /**
     * @param BaseEntity $entity
     * @param array|null $map
     * @param array|null $except
     * @return array
     */
    public function serialize($entity, $map = null, $except = null);

    /**
     * @param Collection|BaseEntity[] $list
     * @param array|null $map
     * @param array|null $except
     * @return array
     */
    public function serializeList($list, $map = null, $except = null);
}
