<?php


namespace App\Util\ConsoleCommandExecutor\ValidationForm;


use App\Util\Task\Task;

/**
 * Class AbstractCrawlerTask
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
abstract class AbstractCrawlerTask extends Task
{
    const IGNORE_CONTROLLER = ['BaseController.php'];

    /** @var array */
    protected $dirs;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->addTitle(static::TITLE);
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->dirs = $data['dirs'];
    }

    protected function run()
    {
        if (empty($this->dirs)) {
            $this->addTextRow('There are no input, searching for directories...');
            $this->defineDirs();
        }

        if (empty($this->dirs)) {
            $this->addErrorRow(static::NOT_FOUND);
            return;
        } else {
            $this->crawlDirs();
        }
    }

    protected abstract function defineDirs();

    private function crawlDirs()
    {
        $this->addTextRow(static::STARTED);
        foreach ($this->dirs as $name) {
            $this->crawlDir($name);
        }
    }

    /**
     * @param string $name
     */
    protected abstract function crawlDir($name);

    /**
     * @param string $path
     * @return array
     */
    protected abstract function scanDir($path);

}