<?php

namespace App\EntitySupport\Behavior;

use App\EntitySupport\Common\BaseEntity;
use App\Entity\ProductAudioBook;
use App\Entity\ProductBook;
use App\Entity\ProductEBook;
use App\Mif;

/**
 * Class BookSynchronizer
 * @package App\Entity\Behavior
 */
trait BookSynchronizer
{
    /**
     * @param string $name
     * @param mixed $value
     */
    protected function afterSetAttribute($name, $value)
    {
        $syncAttributes = ['title', 'originName', 'citation'];
        if (array_search($name, $syncAttributes) === false) {
            return;
        }

        $methods = null;
        switch (static::class) {
            case ProductBook::class:
                $methods = ['getEBook', 'getAudioBook'];
                break;
            case ProductEBook::class:
                $methods = ['getBook', 'getAudioBook'];
                break;
            case ProductAudioBook::class:
                $methods = ['getBook', 'getEBook'];
                break;
        }

        if (!$methods) {
            return;
        }

        foreach ($methods as $method) {
            /** @var BaseEntity $entity */
            $entity = $this->$method();
            if ($entity) {
                $entity->setAttribute($name, $value, true);
                $entity->save();
            }
        }
    }
}
