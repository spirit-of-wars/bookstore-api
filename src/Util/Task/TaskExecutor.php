<?php

namespace App\Util\Task;

use Generator;

/**
 * Class TaskExecutor
 * @package App\Util\Task
 */
class TaskExecutor
{
    /** @var TaskChain */
    protected $taskChain;

    /**
     * TaskExecutor constructor.
     */
    public function __construct()
    {
        $this->taskChain = new TaskChain(static::getContextClass());
    }

    /**
     * @return string
     */
    public static function getContextClass()
    {
        return TaskContext::class;
    }

    /**
     * @return Generator
     */
    protected function run()
    {
        while ($taskResult = $this->taskChain->runNextTask()) {
            yield $taskResult;
        }
    }
}
