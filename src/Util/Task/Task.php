<?php

namespace App\Util\Task;

use App\Util\Common\MessageKeeperTrait;

/**
 * Class Task
 * @package App\Util\Task
 */
abstract class Task
{
    use MessageKeeperTrait;

    /** @var TaskContext */
    private $context;

    /** @var array */
    private $subTasks;

    /**
     * Task constructor.
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->subTasks = [];
        $this->setData($data);
    }

    /**
     * @param TaskContext $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return TaskContext
     */
    public function getContext()
    {
        return $this->context;
    }

    abstract protected function run();

    /**
     * @return TaskResult
     */
    public function runProcess()
    {
        $this->run();
        return new TaskResult($this->message(), $this->subTasks);
    }

    /**
     * @param string $name
     * @param Task $task
     */
    public function addSubTask($name, $task)
    {
        $this->subTasks[$name] = $task;
    }

    /**
     * Can be redefined in children classes
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        // pass
    }

    /**
     * @param string|array $rows
     * @return TaskMessage
     */
    protected function message($rows = [])
    {
        return new TaskMessage(array_merge($this->messages, array($rows), ['']));
    }
}
