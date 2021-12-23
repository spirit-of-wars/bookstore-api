<?php


namespace App\Util\ConsoleCommandExecutor\EntityGenerator;

use App\Mif;
use App\Util\Task\Task;

/**
 * Class AbstractValidationTask
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator
 *
 * Analyzes input entity names or all entity names if input was empty
 * Defines state of each entity:
 * - specification not found
 * - specification is actual
 * - specification need to be updated
 * Creates new tasks for entities need to be updated
 */
abstract class AbstractValidationTask extends Task
{
    /** @var array */
    private $inputNames;

    /**
     * AbstractValidationTask constructor.
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->addTitle('* Task: validation of input entity names');
    }

    protected function run()
    {
        if (empty($this->inputNames)) {
            $this->addTextRow('There are no input, searching for entities...');
            $this->readEntityNames();
        }

        if (empty($this->inputNames)) {
            $this->addErrorRow('Entities not found');
            return;
        } else {
            $this->validateEntityNames();
        }
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->inputNames = $data['entities'];
    }

    private function readEntityNames()
    {
        $path = Mif::getProjectDir() . EntityGeneratorHelper::pathToYamlEntities();
        if (!file_exists($path)) {
            $this->addTextRow("Yaml specifications for entities not found. Directory '$path' doesn't exist.");
            return;
        }

        $this->inputNames = $this->extractYamlSpecificationNames($path);
        if (empty($this->inputNames)) {
            $this->addTextRow("Directory '$path' doesn't contain yaml specifications for entities.");
        } else {
            $this->addTextRow('Yaml specifications for entities have been found...');
        }
    }

    private function validateEntityNames()
    {
        $this->addTextRow('Specifications validation has started...');
        foreach ($this->inputNames as $name) {
            $this->validateEntityName($name);
        }
    }

    /**
     * @param string $name
     */
    protected abstract function validateEntityName($name);

    /**
     * @param string $path
     * @return array
     */
    private function extractYamlSpecificationNames($path)
    {
        $content = scandir($path);
        $list = [];
        foreach ($content as $name) {
            if ($name == '.' || $name == '..') {
                continue;
            }

            if (preg_match('/\.(?:yaml|yml)$/', $name)) {
                preg_match('/(.+?)\.(?:yaml|yml)$/', $name, $match);
                $list[] = $match[1];
                continue;
            }

            if (is_dir($path . '/' . $name)) {
                $subList = $this->extractYamlSpecificationNames($path . '/' . $name);
                foreach ($subList as &$subName) {
                    $subName = $name . '\\' . $subName;
                }
                unset($subName);
                $list = array_merge($list, $subList);
            }
        }
        return $list;
    }
}