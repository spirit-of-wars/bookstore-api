<?php

namespace App\EntitySupport\Common;

use App\Exception\AttributeValidationException;

/**
 * Trait BaseEntityTrait
 * @package App\Entity\Common
 */
trait BaseEntityTrait
{
    /**
     * @param string $name
     * @return mixed
     */
    protected function actionGet($name)
    {
        return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws AttributeValidationException
     */
    protected function actionSet($name, $value)
    {
        $schema = static::getSchema();
        $attribute = $schema->getAttribute($name);
        if (!$attribute) {
            return $this;
        }

        $value = $attribute->preprocessValue($value);
        $value = $this->beforeSetAttribute($name, $value);

        $this->actionSetProcess($name, $value);
        $this->afterSetAttribute($name, $value);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws AttributeValidationException
     */
    protected function actionSetProcess($name, $value)
    {
        $schema = static::getSchema();
        if (!$schema->validate($name, $value)) {
            $className = static::class;
            if (!is_string($value)) {
                $value = json_encode($value);
            }
            throw new AttributeValidationException(
                "{$className} attribute value \${$name}='{$value}' is not valid"
            );
        }
        $this->$name = $value;
    }

    /**
     * @param string $attributeName
     * @param integer|string $key
     * @return mixed
     */
    protected function actionGetItem($attributeName, $key)
    {
        $schema = static::getSchema();
        $attribute = $schema->getAttribute($attributeName);
        return $attribute->onActionGetItem($this->$attributeName, $key);
    }

    /**
     * @param string $attributeName
     * @param array $arguments
     * @return $this
     */
    protected function actionAdd($attributeName, $arguments)
    {
        $schema = static::getSchema();
        $attribute = $schema->getAttribute($attributeName);
        $this->$attributeName = $attribute->onActionAdd($this->$attributeName, $arguments);

        return $this;
    }

    /**
     * @param string $attributeName
     * @param array $arguments
     * @return $this
     */
    protected function actionRemove($attributeName, $arguments)
    {
        $schema = static::getSchema();
        $attribute = $schema->getAttribute($attributeName);
        $this->$attributeName = $attribute->onActionRemove($this->$attributeName, $arguments);

        return $this;
    }

    /**
     * @param string $name
     * @return object
     */
    protected function actionRelationGet($name)
    {
        return $this->$name;
    }

    /**
     * @param string $name
     * @param object $value
     * @return $this
     */
    protected function actionRelationSet($name, $value)
    {
        $this->$name = $value;
        $relation = static::getSchema()->getRelation($name);
        if ($relation->isOneToOne() && !$relation->isUni() && !$relation->isInversed()) {
            $relMethod = EntityConstants::ACTION_SET . ucfirst($relation->getTargetRelationName());
            $value->$relMethod($this);
        }
        
        return $this;
    }

    /**
     * @param string $name
     * @param object $value
     * @return $this
     */
    protected function actionRelationAdd($name, $value)
    {
        $relation = static::getSchema()->getRelation($name);
        $relMethod = $relation->getRelatedSetterName();
        if ($relation->isOneToMany()) {
            if (!$this->$name->contains($value)) {
                $this->$name[] = $value;
                $value->$relMethod($this);
            }
        } elseif ($relation->isManyToMany()) {
            if (!$this->$name->contains($value)) {
                if (!$relation->isInversed()) {
                    $value->$relMethod($this);
                }
                $this->$name[] = $value;
            }
        } elseif ($relation->isToOne()) {
            $this->$name = $value;
            $value->$relMethod($this);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param object $value
     * @return $this
     */
    protected function actionRelationRemove($name, $value)
    {
        $relation = static::getSchema()->getRelation($name);
        if ($relation->isOneToMany()) {
            if ($this->$name->contains($value)) {
                $this->$name->removeElement($value);
                $relGetMethod = EntityConstants::ACTION_GET . ucfirst($relation->getTargetRelationName());
                if ($value->$relGetMethod() === $this) {
                    $relSetMethod = EntityConstants::ACTION_SET . ucfirst($relation->getTargetRelationName());
                    $value->$relSetMethod(null);
                }
            }
        } elseif ($relation->isManyToMany()) {
            if ($this->$name->contains($value)) {
                $this->$name->removeElement($value);
            }
            if (!$relation->isInversed()) {
                $relMethod = EntityConstants::ACTION_REMOVE . ucfirst($relation->getTargetRelationName());
                $value->$relMethod($this);
            }
        }
        return $this;
    }
}
