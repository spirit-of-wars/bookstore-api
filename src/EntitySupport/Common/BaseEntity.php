<?php

namespace App\EntitySupport\Common;

use App\Exception\BadRequestException;
use App\Exception\MethodCallException;
use App\Mif;
use App\Service\Entity\EntityService;
use Doctrine\ORM\PersistentCollection;
use Exception;

/**
 * Class BaseEntity
 * @package App\Entity\Common
 *
 * @method integer getId()
 */
abstract class BaseEntity
{
    /** @var array */
    private static $schemaMap = [];

    /**
     * This method can be redefined by children classes.
     * Returns array for attributes which are not available for setting action.
     *
     * @return array
     */
    public static function getReadOnlyAttributesList()
    {
        return ['id'];
    }

    /**
     * @return EntitySchema
     */
    public static function getSchema()
    {
        $className = self::getClassName(static::class);
        if (!array_key_exists($className, self::$schemaMap)) {
            self::loadSchema($className);
        }

        return self::$schemaMap[$className] ?? new EntitySchema();
    }

    /**
     * @return EntityService|null
     */
    public static function getService()
    {
        $className = self::getClassName(static::class);
        if (array_key_exists($className, EntityServiceMap::MAP)) {
            $serviceName = EntityServiceMap::MAP[$className];
            /** @var EntityService $service */
            $service = Mif::getServiceProvider()->$serviceName;
            return $service;
        }

        $service = new TempEntityService($className);
        return $service;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|$this
     * @throws MethodCallException
     */
    public function __call($name, $arguments)
    {
        $className = self::getClassName(static::class);
        if (method_exists($this, $name)) {
            throw new MethodCallException("Method is not public {$className}::{$name}");
        }

        list ($action, $attributeName) = $this->parseActionCall($name);
        if ($action) {
            if ($this->validateAttributeActionCall($action, $attributeName)) {
                switch ($action) {
                    case EntityConstants::ACTION_GET:
                        return $this->actionGet($attributeName);
                    case EntityConstants::ACTION_SET:
                        return $this->actionSet($attributeName, $arguments[0]);
                    case EntityConstants::ACTION_GET_ITEM:
                        return $this->actionGetItem($attributeName, $arguments[0]);
                    case EntityConstants::ACTION_ADD:
                        return $this->actionAdd($attributeName, $arguments);
                    case EntityConstants::ACTION_REMOVE:
                        return $this->actionRemove($attributeName, $arguments);
                }
            }

            if ($this->validateRelationActionCall($action, $attributeName)) {
                switch ($action) {
                    case EntityConstants::ACTION_GET:
                        return $this->actionRelationGet($attributeName);
                    case EntityConstants::ACTION_SET:
                        return $this->actionRelationSet($attributeName, $arguments[0]);
                    case EntityConstants::ACTION_ADD:
                        return $this->actionRelationAdd($attributeName, $arguments[0]);
                    case EntityConstants::ACTION_REMOVE:
                        return $this->actionRelationRemove($attributeName, $arguments[0]);
                }
            }
        }

        throw new MethodCallException("Method doesn't exist {$className}::{$name}");
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        return $this->actionGet($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param bool $force
     * @return $this
     */
    public function setAttribute($name, $value, $force = false)
    {
        if ($force) {
            $this->actionSetProcess($name, $value);
            return $this;
        }

        return $this->actionSet($name, $value);
    }

    /**
     * @param array $map
     * @param array $except
     * @return array
     */
    public function getAttributes($map = null, $except = [])
    {
        $result = [];
        $schema = static::getSchema();
        $attributes = $schema->getAttributeNames();
        foreach ($attributes as $attribute) {
            if (is_array($map) && array_search($attribute, $map) === false) {
                continue;
            }

            if (array_search($attribute, $except) !== false) {
                continue;
            }

            $result[$attribute] = $this->actionGet($attribute);
        }
        return $result;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $name => $attribute) {
            $this->setAttribute($name, $attribute);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function setRelation($name, $value)
    {
        $schema = $this->getSchema();
        $relation = $schema->getRelation($name);
        if (!$relation) {
            return;
        }

        if ($relation->isToMany()) {
            $getter = $relation->getGetterName();
            $setter = $relation->getSetterName();
            $remover = $relation->getRemoverName();

            $newRelatedIds = [];
            foreach ($value as $item) {
                if (is_int($item)) {
                    if ($item == 0) {
                        throw new BadRequestException("Передан id = 0");
                    }

                    $newRelatedIds[] = $item;
                } elseif ($item instanceof BaseEntity) {
                    $newRelatedIds[] = $item->getId();
                }
            }
            $list = $this->$getter();
            $map = [];
            /** @var BaseEntity $item */
            foreach ($list as $item) {
                $map[$item->getId()] = $item;
            }

            $idsForAdd = array_diff($newRelatedIds, array_keys($map));
            $idsForDelete = array_diff(array_keys($map), $newRelatedIds);
            foreach ($idsForDelete as $id) {
                $this->$remover($map[$id]);
            }

            $repo = $relation->getTargetRepository();
            $relEntities = $repo->findBy(['id' => $idsForAdd]);
            foreach ($relEntities as $relEntity) {
                $this->$setter($relEntity);
            }
        } else {
            $setter = $relation->getSetterName();
            $newRelatedId = null;
            if (is_int($value)) {
                if ($value == 0) {
                    throw new BadRequestException("Передан id = 0");
                }

                $newRelatedId = $value;
            } elseif (is_array($value)) {
                $newRelatedId = $value[0];
            } elseif ($value instanceof BaseEntity) {
                $newRelatedId = $value->getId();
            }
            if ($newRelatedId === null) {
                $this->$setter(null);
            } else {
                $repo = $relation->getTargetRepository();
                $relEntity = $repo->find($newRelatedId);
                if (!$relEntity) {
                    throw new BadRequestException("Объект не найден: id = " . $newRelatedId);
                }

                $this->$setter($relEntity);
            }
        }
    }

    /**
     * @param array|null $map
     * @param array $except
     * @return array
     */
    public function getProperties($map = null, $except = [])
    {
        $result = [];

        $schema = static::getSchema();
        $properties = $schema->getProperties();
        foreach ($properties as $name => $property) {
            if (is_array($map) && array_search($name, $map) === false && !array_key_exists($name, $map)) {
                continue;
            }

            if (array_search($name, $except) !== false) {
                continue;
            }

            if ($property->isRelation()) {
                /** @var EntityRelation $property */
                $relatedEntities = $property->extractAsArray($this);

                $relMap = (is_array($map) && array_key_exists($name, $map)) ? $map[$name] : null;
                $relExcept = array_key_exists($name, $except) ? $except[$name] : [];

                /** @var BaseEntity $relatedEntity */
                foreach ($relatedEntities as $relatedEntity) {
                    $result[$name][] = $relatedEntity->getAttributes($relMap, $relExcept);
                }
            } else {
                $result[$name] = $this->getAttribute($name);
            }
        }

        return $result;
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function setProperties($properties)
    {
        foreach ($properties as $name => $value) {
            if ($this->hasRelation($name)) {
                if (is_null($value)) {
                    continue;
                }

                if (is_array($value)) {
                    if (empty($value)) {
                        continue;
                    }

                    foreach ($value as $attribute) {
                        $this->setRelatedEntity($name, $attribute);
                    }
                } else {
                    $this->setRelatedEntity($name, $value);
                }
            } else {
                $this->actionSet($name, $value);
            }
        }

        return  $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRelation($name)
    {
        $schema = static::getSchema();
        return $schema->hasRelation($name);
    }

    /**
     * @param string $name
     * @return EntityRelation|null
     */
    public function getRelation($name)
    {
        $schema = static::getSchema();
        if ($schema->hasRelation($name)) {
            return $schema->getRelation($name);
        }

        return null;
    }

    /**
     * @param string $name
     * @return BaseEntity|BaseEntity[]|null
     */
    public function getRelatedEntity($name)
    {
        if ($this->hasRelation($name)) {
            $getter = $this->getRelation($name)->getGetterName();
            return $this->$getter();
        }

        return null;
    }

    /**
     * @param string $name
     * @param BaseEntity|int $entity
     */
    public function setRelatedEntity($name, $entity)
    {
        if ($this->hasRelation($name)) {
            if ($entity === null) {
                $this->actionRelationAdd($name, null);
                return;
            }

            if (is_int($entity)) {
                if ($entity == 0) {
                    throw new BadRequestException("Передан id = 0");
                }

                $schema = static::getSchema();
                $relation = $schema->getRelation($name);
                $repo = $relation->getTargetRepository();
                $id = $entity;
                $entity = $repo->find($entity);
                if (!$entity) {
                    throw new BadRequestException("Объект не найден: id = " . $id);
                }
            }

            $this->actionRelationAdd($name, $entity);
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    abstract protected function actionGet($name);

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    abstract protected function actionSet($name, $value);

    /**
     * @param string $name
     * @param mixed $value
     */
    abstract protected function actionSetProcess($name, $value);

    /**
     * @param string $attributeName
     * @param integer|string $key
     * @return mixed
     */
    abstract protected function actionGetItem($attributeName, $key);

    /**
     * @param string $attributeName
     * @param array $arguments
     * @return $this
     */
    abstract protected function actionAdd($attributeName, $arguments);

    /**
     * @param string $attributeName
     * @param array $arguments
     * @return $this
     */
    abstract protected function actionRemove($attributeName, $arguments);

    /**
     * @param string $name
     * @return object
     */
    abstract protected function actionRelationGet($name);

    /**
     * @param string $name
     * @param object $value
     * @return $this
     */
    abstract protected function actionRelationSet($name, $value);

    /**
     * @param string $name
     * @param object $value
     * @return $this
     */
    abstract protected function actionRelationAdd($name, $value);

    /**
     * @param string $name
     * @param object $value
     * @return $this
     */
    abstract protected function actionRelationRemove($name, $value);

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function beforeSetAttribute($name, $value)
    {
        return $value;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    protected function afterSetAttribute($name, $value)
    {
        // pass
    }

    /**
     * @param string $name
     * @return array
     */
    private function parseActionCall($name)
    {
        $regExp = '/^('
            . EntityConstants::ACTION_GET_ITEM
            . '|' . EntityConstants::ACTION_GET
            . '|' . EntityConstants::ACTION_SET
            . '|' . EntityConstants::ACTION_GET_ITEM
            . '|' . EntityConstants::ACTION_ADD
            . '|' . EntityConstants::ACTION_REMOVE
            . ')(.+)$/';
        preg_match($regExp, $name, $matches);
        if (empty($matches)) {
            return [null, null];
        }

        return [$matches[1], lcfirst($matches[2])];
    }

    /**
     * @return bool
     */
    public function save()
    {
        Mif::getPersistHolder()->persistEntity($this);

        return Mif::getPersistHolder()->tryFlush($this);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        Mif::getPersistHolder()->removeEntity($this);

        return Mif::getPersistHolder()->tryFlush($this);
    }

    /**
     * @return bool
     */
    public function isNew() {
        return $this->getId() === null;
    }

    /**
     * @param string $action
     * @param string $attributeName
     * @return bool
     */
    private function validateAttributeActionCall($action, $attributeName)
    {
        $schema = static::getSchema();
        if (!$schema->hasAttribute($attributeName)) {
            return false;
        }

        $attribute = $schema->getAttribute($attributeName);
        if (!$attribute->actionIsAllow($action)) {
            return false;
        }

        if ($action == EntityConstants::ACTION_SET
            && (array_search($attributeName, static::getReadOnlyAttributesList()) !== false)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param string $action
     * @param string $relationName
     * @return bool
     */
    private function validateRelationActionCall($action, $relationName)
    {
        $schema = static::getSchema();
        if (!$schema->hasRelation($relationName)) {
            return false;
        }

        $relation = $schema->getRelation($relationName);
        if (!$relation->actionIsAllow($action)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $className
     */
    private static function loadSchema($className)
    {
        try {
            self::$schemaMap[$className] = EntitySchema::createForClass($className);
        } catch (Exception $e) {
            // pass
        }
    }

    /**
     * @param string $className
     * @return string
     */
    private static function getClassName($className)
    {
        if (preg_match('/^Proxies\\\__CG__\\\(.+)$/', $className, $matches)) {
            return $matches[1];
        }

        return $className;
    }
}
