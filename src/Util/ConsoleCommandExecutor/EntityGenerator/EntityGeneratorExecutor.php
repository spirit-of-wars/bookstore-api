<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator;

use App\Util\Task\TaskExecutor;
use Generator;

/**
 * Class Core
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator
 */
class EntityGeneratorExecutor extends TaskExecutor
{
    /**
     * @param array $entities
     * @param array $flags
     * @return Generator
     */
    public function generatePhp($entities, $flags)
    {
        $this->taskChain->init([
            'validation' => new ValidationTask([
                'entities' => $entities,
            ])
        ], $flags);
        return $this->run();
    }

    /**
     * @param array $entities
     * @param array $flags
     * @return Generator
     */
    public function compile($entities, $flags = [])
    {
        $this->taskChain->init([
            'validation' => new ValidationTaskDescription([
                'entities' => $entities,
            ])
        ], $flags);
        return $this->run();
    }
}
