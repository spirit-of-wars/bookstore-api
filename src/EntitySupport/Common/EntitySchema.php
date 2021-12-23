<?php

namespace App\EntitySupport\Common;

use App\EntitySupport\Common\Attribute\EntityAttribute;
use App\Exception\AttributeValidationException;
use App\Helper\DocHelper;
use App\Helper\NamespaceHelper;
use ReflectionClass;
use ReflectionException;

/**
 * Class EntitySchema
 * @package App\Entity\Common
 */
class EntitySchema
{
    /** @var string */
    private $entityClassName;

    /** @var EntityAttribute[] */
    private $attributes = [];

    /** @var EntityRelation[] */
    private $relations = [];

    /** @var array */
    private $methods = [];

    /**
     * EntitySchema constructor.
     * @param $entityClassName
     * @param array $schema
     */
    public function __construct($entityClassName = '', $schema = [])
    {
        $this->entityClassName = $entityClassName;
        if (isset($schema['attributes'])) {
            foreach ($schema['attributes'] as $attributeName => $attributeData) {
                $this->attributes[$attributeName] = EntityAttribute::create($attributeName, $attributeData);
            }
        }

        if (isset($schema['relations'])) {
            foreach ($schema['relations'] as $relationName => $relationData) {
                $this->relations[$relationName] = new EntityRelation($relationName, $relationData);
            }
        }

        foreach ($this->attributes as $attribute) {
            $actions = $attribute->getAllowedActions();
            $name = ucfirst($attribute->getName());
            foreach ($actions as $action) {
                $this->methods[] = $action . $name;
            }
        }

        foreach ($this->relations as $relation) {
            $actions = $relation->getAllowedActions();
            $name = ucfirst($relation->getName());
            foreach ($actions as $action) {
                $this->methods[] = $action . $name;
            }
        }
    }

    /**
     * @return string
     */
    public function getEntityClassName()
    {
       return $this->entityClassName;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        $slug = str_replace(NamespaceHelper::getEntityDefaultNamespace(), '', $this->entityClassName);
        $slug = str_replace('\\', '_', $slug);
        $slug = strtolower($slug);
        return $slug;
    }

    /**
     * @param $className
     * @return EntitySchema
     * @throws ReflectionException
     */
    public static function createForClass($className)
    {
        $schemaMap = self::parseClass($className);
        return new self($className, $schemaMap);
    }

    /**
     * @param $className
     * @return array
     * @throws ReflectionException
     */
    public static function parseClass($className)
    {
        $reflection = new ReflectionClass($className);
        $properties = $reflection->getProperties();
        $defaults = $reflection->getDefaultProperties();
        foreach ($defaults as $key => $value) {
            if ($value === null) {
                unset($defaults[$key]);
            }
        }

        $columnRegExp = '/' . addcslashes(EntityConstants::ATTRIBUTE_ORM_DATA, '\\') . '/';
        $relRegExp ='/\\\(?:ManyToMany|OneToOne|OneToMany|ManyToOne)/';
        $schemaMap = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $doc = DocHelper::parseDocComment($property->getDocComment());
            $keys = array_keys($doc);
            if (!empty(preg_grep($columnRegExp, $keys))) {
                if (array_key_exists($name, $defaults)) {
                    $doc[EntityConstants::ATTRIBUTE_ORM_DATA]['default'] = $defaults[$name];
                }
                $schemaMap['attributes'][$name] = $doc;
            } elseif (!empty(preg_grep($relRegExp, $keys))) {
                $schemaMap['relations'][$name] = $doc;
            }
        }

        return $schemaMap;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->attributes);
    }

    /**
     * @return array
     */
    public function getAttributeNames()
    {
        return array_keys($this->attributes);
    }

    /**
     * @return array
     */
    public function getRelationNames()
    {
        return array_keys($this->relations);
    }

    /**
     * @param string $name
     * @return EntityAttribute
     */
    public function getAttribute($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * @param string $name
     * @return EntityRelation
     */
    public function getRelation($name)
    {
        return $this->relations[$name] ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRelation($name)
    {
        return array_key_exists($name, $this->relations);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasMethod($name)
    {
        return in_array($name, $this->methods);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     * @throws AttributeValidationException
     */
    public function validate($name, $value)
    {
        if (!$this->hasAttribute($name)) {
            return false;
        }

        $attribute = $this->getAttribute($name);
        return $attribute->validate($value);
    }

    /**
     * @return EntityProperty[]
     */
    public function getProperties()
    {
        return array_merge($this->attributes, $this->relations);
    }

    /**
     * @return EntityAttribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return EntityRelation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
