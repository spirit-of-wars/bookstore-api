<?php

namespace App\Util\Task;

/**
 * Class TaskContext
 * @package App\Util\Task
 */
class TaskContext
{
    /** @var TaskChain */
    protected $chain;

    /** @var array */
    private $data;

    /**
     * TaskContext constructor.
     * @param TaskChain $chain
     */
    public function __construct($chain)
    {
        $this->chain = $chain;
        $this->data = [];
    }

    /**
     * @return TaskChain
     */
    public function getTaskChain()
    {
        return $this->chain;
    }

    /**
     * @param array $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addData($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getData($key) {
        return $this->data[$key] ?? null;
    }
}
