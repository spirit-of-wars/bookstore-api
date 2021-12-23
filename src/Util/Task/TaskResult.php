<?php

namespace App\Util\Task;

/**
 * Class TaskResult
 * @package App\Util\Task
 */
class TaskResult
{
    const TYPE_MESSAGE = 'message';
    const TYPE_ACTION_NEW_TASKS = 'action_new_tasks';

    /** @var string */
    private $type;

    /** @var TaskMessage */
    private $message;

    /** @var array */
    private $extraTasks;

    public function __construct($message, $extraTasks = null)
    {
        $this->message = $message;
        $this->extraTasks = $extraTasks;

        $this->type = ($extraTasks === null || empty($extraTasks))
            ? self::TYPE_MESSAGE
            : self::TYPE_ACTION_NEW_TASKS;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return TaskMessage
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getExtraTasks()
    {
        return $this->extraTasks;
    }
}
