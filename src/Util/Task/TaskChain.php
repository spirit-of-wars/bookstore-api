<?php

namespace App\Util\Task;

/**
 * Class TaskChain
 * @package App\Util\Task
 */
class TaskChain
{
    /** @var TaskContext */
    private $context;

    /** @var array */
    private $taskList;

    /** @var array */
    private $taskKeys;

    /** @var int */
    private $currentTaskIndex;

    /**
     * TaskChain constructor.
     * @param string $contextClass
     */
    public function __construct($contextClass)
    {
        $this->context = new $contextClass($this);
    }

    /**
     * @return TaskContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $list
     * @param array $data
     */
    public function init($list, $data = [])
    {
        $this->taskList = $list;
        $this->taskKeys = array_keys($this->taskList);
        $this->currentTaskIndex = 0;
        $this->context->setData($data);

        /** @var Task $task */
        foreach ($this->taskList as $task) {
            $task->setContext($this->context);
        }
    }

    /**
     * @param $key
     * @return Task|null
     */
    public function getTask($key)
    {
        return $this->taskList[$key] ?? null;
    }

    /**
     * @return TaskMessage
     */
    public function runNextTask()
    {
        if ($this->currentTaskIndex >= count($this->taskKeys)) {
            return null;
        }

        /** @var Task $task */
        $taskKey = $this->taskKeys[$this->currentTaskIndex];
        $task = $this->taskList[$taskKey];
        $taskResult = $task->runProcess();
        if ($taskResult->getType() == TaskResult::TYPE_ACTION_NEW_TASKS) {
            $this->addTasksAfter($taskResult->getExtraTasks(), $taskKey);
        }
        $this->currentTaskIndex++;

        $result = $taskResult->getMessage();
        return $result;
    }

    /**
     * @param array $extraTasks
     * @param string $taskKey
     */
    private function addTasksAfter($extraTasks, $taskKey)
    {
        $newList = [];
        foreach ($this->taskList as $key => $task) {
            $newList[$key] = $task;
            if ($key == $taskKey) {
                /**@var Task $newTask */
                foreach ($extraTasks as $newKey => $newTask) {
                    $newList[$newKey] = $newTask;
                    $newTask->setContext($this->context);
                }
            }
        }

        $this->taskList = $newList;
        $this->taskKeys = array_keys($this->taskList);
    }
}
