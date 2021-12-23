<?php

namespace App\Util\ConsoleCommandExecutor\ValidationForm;

use App\Util\Task\TaskExecutor;

/**
 * Class ValidationFormExecutor
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class ValidationFormExecutor extends TaskExecutor
{
    /**
     * @return string
     */
    public static function getContextClass()
    {
        return TaskContext::class;
    }

    /**
     * @param array $controllerName
     * @return \Generator
     */
    public function renewUtilForController($controllerName = [])
    {
        $this->taskChain->init([
            'validation' => new ControllerCrawlerTask([
                'dirs' => $controllerName,
            ])
        ]);
        return $this->run();
    }

    /**
     * @param array $dirs
     * @param array $flags
     * @return \Generator
     */
    public function compile ($dirs = [], $flags = [])
    {
        $this->taskChain->init([
            'validation' => new FormCrawlerTask([
                'dirs' => $dirs,
            ])
        ], $flags);
        return $this->run();
    }

    /**
     * @return void
     */
    public function finalizeCompile()
    {
        /** @var TaskContext $context */
        $context = $this->taskChain->getContext();
        $context->saveMap();
    }
}
