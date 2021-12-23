<?php

namespace App\Util\Task;

/**
 * Class Message
 * @package App\Util\Task
 */
class TaskMessage
{
    /** @var string|string[] */
    private $text;

    /**
     * TaskMessage constructor.
     * @param string|string[] $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * @return string|string[]
     */
    public function getMessage()
    {
        return $this->text;
    }

}
