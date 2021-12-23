<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer;

/**
 * Class Schema
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer
 */
class Schema
{
    /** @var array */
    private $data;

    /**
     * Schema constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        $this->data = $config;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->data['name'] ?? '';
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->data['table'] ?? '';
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->data['attributes'] ?? [];
    }

    /**
     * @return array
     */
    public function getExtraAttributes()
    {
        return $this->data['extraAttributes'] ?? [];
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->data['relations'] ?? [];
    }

    /**
     * @return array
     */
    public function getForUse()
    {
        return $this->data['forUse'] ?? [];
    }

    /**
     * @return array
     */
    public function getInterfaces()
    {
        return $this->data['interfaces'] ?? [];
    }

    /**
     * @return array
     */
    public function getForTraits()
    {
        return $this->data['forTraits'] ?? [];
    }

    /**
     * @return array
     */
    public function getOrmExtensions()
    {
        return $this->data['ormExtensions'] ?? [];
    }
}
