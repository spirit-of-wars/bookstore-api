<?php

namespace App\Util\ConsoleCommandExecutor\ValidationForm;

use App\Command\ValidationForm\CompileCommand;
use App\Mif;
use App\Util\Task\Task;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TargetCrawlerTask
 * @package App\Util\ConsoleCommandExecutor\ValidationForm
 */
class TargetCrawlerTask extends Task
{
    /** @var string */
    private $formsDirName;

    /** @var array */
    private $formFileNames;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->addTitle("* Task: crawling for <fg=magenta>{$this->formsDirName}</> validation forms");
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->formsDirName = $data['formsDirName'];
    }

    protected function run()
    {
        if (!$this->defineFormFileNames()) {
            return;
        }

        $this->processFormFileNames();
    }

    /**
     * @return bool
     */
    private function defineFormFileNames()
    {
        if (!$this->formsDirName) {
            $this->addErrorRow('There is no form directory name');
            return false;
        }

        $path = ValidationFormHelper::getUtilPath() . '/' . $this->formsDirName;
        if (!file_exists($path)) {
            $this->addErrorRow("Directory '$path' doesn't exist");
            return false;
        }

        $this->formFileNames = $this->scanDir($path);
        if (empty($this->formFileNames)) {
            $this->addErrorRow("Directory '$path' doesn't contain validation form specifications");
            return false;
        }

        return true;
    }

    private function processFormFileNames()
    {
        $this->addTextRow('Specifications validation has started...');
        foreach ($this->formFileNames as $path) {
            $this->validateFile($path);
        }
    }

    /**
     * @param string $path
     */
    private function validateFile($path)
    {
        $this->addTextRow("Validating for file <fg=magenta>'$path'</> is running...");
        $row = 'Result: ';
        if (!file_exists($path)) {
            $this->addTextRow($row . 'file doesn\'t exist');
            return;
        }

        $data = Yaml::parseFile($path);
        $formsTodo = [];
        if (!is_array($data['InputForm'])) {
            $formsTodo[] = 'InputForm has to be defined';
        }
        if (!is_array($data['OutputForm'])) {
            $formsTodo[] = 'OutputForm has to be defined';
        }
        if (!empty($formsTodo)) {
            $this->addErrorRow($row . implode('. ', $formsTodo));
            return;
        }

        $metaData = $data['MetaData'];
        if (!isset($metaData['Controller']) || !isset($metaData['Action']) || !isset($metaData['Method'])) {
            $this->addTextRow(
                $row . 'MetaData is damaged. It has to have fields: "Controller", "Action", "Method"'
            );
            return;
        }

        /** @var TaskContext $context */
        $context = $this->getContext();
        $needUpdate = $this->checkNeedUpdate($path, $metaData);
        if ($needUpdate) {
            if ($context->getData(CompileCommand::MODE_SHOW_STATUS)) {
                $this->addTextRow($row . '<fg=yellow>need update</>');
            } else {
                $this->addTextRow($row . '<fg=yellow>need update. I\'m creating a new task...</>');
                $this->addSubTask(
                    $this->formsDirName . '_' . $metaData['Method'] . '_' . $metaData['Action'],
                    new FormCompilerTask([
                        'group' => $this->formsDirName,
                        'sourceFile' => $path,
                        'data' => $data,
                    ])
                );
            }
        } else {
            if (!$context->getData(CompileCommand::MODE_NEED_UPDATE)) {
                $this->addTextRow($row . '<fg=green>current state is actual</>');
            }
        }
    }

    /**
     * @param string $path
     * @param array $metaData
     * @return boolean
     */
    private function checkNeedUpdate($path, $metaData)
    {
        /** @var TaskContext $context */
        $context = $this->getContext();
        if ($context->getData(CompileCommand::MODE_FORCE)) {
            $needUpdate = true;
        } else {
            $targetFiles = $context->getCompiledFormFileNames($metaData);
            if (empty($targetFiles)) {
                $needUpdate = true;
            } else {
                $needUpdate = false;
                $sourceFiles = $context->getEntityFileNames($metaData);
                $sourceFiles[] = $path;
                foreach ($targetFiles as $targetFile) {
                    foreach ($sourceFiles as $sourceFile) {
                        $needUpdate = filemtime($sourceFile) > filemtime($targetFile);
                        if ($needUpdate) {
                            return $needUpdate;
                        }
                    }
                }
            }
        }

        return $needUpdate;
    }

    /**
     * @param string $path
     * @return array
     */
    private function scanDir($path)
    {
        $content = scandir($path);
        $list = [];
        foreach ($content as $item) {
            if (preg_match('/\.(?:yaml|yml)$/', $item)) {
                $list[] = $path . '/' . $item;
            }
        }
        return $list;
    }
}
