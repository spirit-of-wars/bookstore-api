<?php

namespace App\EntitySupport\Common;

/**
 * Class EntityProperty
 * @package App\EntitySupport\Common
 */
abstract class EntityProperty
{
    /** @var string */
    protected $name;

    /**
     * EntityProperty constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $actionName
     * @return bool
     */
    public function actionIsAllow($actionName)
    {
        return (array_search($actionName, $this->getAllowedActions()) !== false);
    }

    /**
     * @return array
     */
    abstract public function getAllowedActions();

    /**
     * @return bool
     */
    abstract function isRelation();
}
