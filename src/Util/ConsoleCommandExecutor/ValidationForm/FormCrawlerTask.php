<?php

namespace App\Util\ConsoleCommandExecutor\ValidationForm;

/**
 * Class FormCrawlerTask
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class FormCrawlerTask extends AbstractCrawlerTask
{
    const TITLE = '* Task: crawling for all validation forms';
    const NOT_FOUND = 'Validation forms not found';
    const STARTED = 'Specifications validation has started...';

    protected function defineDirs()
    {
        $path = ValidationFormHelper::getUtilPath();
        if (!file_exists($path)) {
            $this->addTextRow(
                "Yaml specifications for validation forms not found. Directory '$path' doesn't exist."
            );
            return;
        }

        $this->dirs = $this->scanDir($path);
        if (empty($this->dirs)) {
            $this->addTextRow("Directory '$path' doesn't contain yaml specifications for validation forms.");
        } else {
            $this->addTextRow('Yaml specifications for validation forms have been found...');
        }
    }

    /**
     * @param string $name
     */
    protected function crawlDir($name)
    {
        $this->addTextRow("- Forms for '<fg=magenta>$name</>' found: <fg=yellow>I'm creating a new task...</>");

        $this->addSubTask($name, new TargetCrawlerTask([
            'formsDirName' => $name,
        ]));
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

            if (!is_dir($path . '/' . $item)) {
                continue;
            }

            $list[] = $item;

        }

        return $list;
    }
}
