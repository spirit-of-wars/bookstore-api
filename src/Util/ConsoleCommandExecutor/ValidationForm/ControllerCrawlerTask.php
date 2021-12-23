<?php


namespace App\Util\ConsoleCommandExecutor\ValidationForm;

/**
 * Class ControllerCrawlerTask
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class ControllerCrawlerTask extends AbstractCrawlerTask
{

    const TITLE = '* Task: crawling for all controllers';
    const NOT_FOUND = 'Controllers not found';
    const STARTED = 'Controller have started...';

    protected function defineDirs()
    {
        $path = ValidationFormHelper::getControllerPath();
        if (!file_exists($path)) {
            $this->addTextRow(
                "Controller for yaml specifications not found. Directory '$path' doesn't exist."
            );
            return;
        }

        $this->dirs = $this->scanDir($path);
        if (empty($this->dirs)) {
            $this->addTextRow("Directory '$path' doesn't contain Controller for yaml specifications");
        } else {
            $this->addTextRow('Controller for yaml specifications have been found...');
        }
    }

    /**
     * @param string $name
     */
    protected function crawlDir($name)
    {
        $this->addTextRow("- Controller for '<fg=magenta>$name</>' found: <fg=yellow>I'm creating a new task...</>");

        $this->addSubTask($name, new RenewControllerUtilTask($name));
    }

    /**
     * @param string $path
     * @return array
     */
    protected function scanDir($path)
    {
        $content = scandir($path);
        $list = [];
        foreach ($content as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if ($item[0] === '.' || in_array($item, self::IGNORE_CONTROLLER)) {
                continue;
            }

            $list[] = str_replace(".php", "", $item);

        }
        return $list;
    }
}