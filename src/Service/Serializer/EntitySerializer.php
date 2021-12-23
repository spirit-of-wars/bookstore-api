<?php

namespace App\Service\Serializer;

use App\EntitySupport\Common\BaseEntity;
use App\Interfaces\EntitySerializerInterface;
use DateTime;
use Doctrine\Common\Collections\Collection;

/**
 * Class EntitySerializer
 * @package App\Service\Serializer
 */
class EntitySerializer implements EntitySerializerInterface
{
    /**
     * @param BaseEntity $entity
     * @param array|null $map
     * @param array|null $except
     * @return array
     */
    public function serialize($entity, $map = null, $except = null)
    {
        if ($except === null) {
            $except = ['createdAt', 'updatedAt'];
        }

        $attributes = $entity->getAttributes($map, $except);
        return $this->serializeAttributes($attributes);
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function serializeAttributes($attributes)
    {
        $result = $attributes;
        foreach ($result as &$attribute) {
            if ($attribute instanceof DateTime) {
                $attribute = $attribute->format('Y-m-d\TH:i:sP');
            }
        }
        unset ($attribute);

        return $result;
    }

    /**
     * @param Collection|BaseEntity[] $list
     * @param array|null $map
     * @param array|null $except
     * @return array
     */
    public function serializeList($list, $map = null, $except = null)
    {
        if ($except === null) {
            $except = ['createdAt', 'updatedAt'];
        }

        $result = [];
        foreach ($list as $entity) {
            $result[] = $this->serialize($entity, $map, $except);
        }

        return $result;
    }
}
