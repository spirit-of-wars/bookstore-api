<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator;

use App\Mif;
use App\Util\Task\Task;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer\CodeRenderer;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer\SchemaParser;

/**
 * Class EntityTask
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator
 */
class EntityTask extends Task
{
    /** @var string */
    private $entityName;

    /**
     * EntityTask constructor.
     * @param string $entityName
     */
    public function __construct($entityName)
    {
        parent::__construct();
        $this->entityName = $entityName;
        $this->addTitle("* Task: entity '<fg=magenta>{$entityName}</>' updating");
    }

    protected function run()
    {
        $parser = new SchemaParser($this->entityName);
        $parser->run();
        $this->addTextRows($parser->getMessages());
        if ($parser->hasErrors()) {
            return;
        }

        $renderer = new CodeRenderer($parser->getSchema());
        $renderer->run();
        $this->addTextRows($renderer->getMessages());
        if ($renderer->hasErrors()) {
            return;
        }

        $this->renewFiles($renderer->getCode());
        $this->addTextRow('Entity updated.');
    }

    /**
     * @param string $code
     */
    private function renewFiles($code)
    {
        $repoFileName = Mif::getProjectDir() . EntityGeneratorHelper::getEntityRepositoryPath($this->entityName);
        if (!file_exists($repoFileName)) {
            $repoCode = $this->prepareRepositoryCodeText();
            $this->prepareDirectory($repoFileName);
            file_put_contents($repoFileName, $repoCode);
        }

        $targetFileName = Mif::getProjectDir() . EntityGeneratorHelper::getEntityPath($this->entityName);
        $this->prepareDirectory($targetFileName);
        file_put_contents($targetFileName, $code);
    }

    /**
     * @return string
     */
    private function prepareRepositoryCodeText()
    {
        $template = $this->getRepoTemplate();

        $namespace = EntityGeneratorHelper::getEntityNamespace($this->entityName);
        $className = EntityGeneratorHelper::getEntitySimpleName($this->entityName);
        $namespaceName = $namespace == ''
            ? '\\' . $className
            : '\\' . $namespace . '\\' . $className;
        $namespace = ($namespace == '') ? '' : ('\\' . $namespace);

        $template = str_replace('<namespace>', $namespace, $template);
        $template = str_replace('<namespace_name>', $namespaceName, $template);
        $template = str_replace('<name>', $className, $template);
        return $template;
    }

    /**
     * @param string $fileName
     */
    private function prepareDirectory($fileName)
    {
        $dirName = dirname($fileName);
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
    }

    /**
     * @return string
     */
    private function getRepoTemplate()
    {
        return file_get_contents(__DIR__ . '/EntityRenderer/tpl/repoTemplate');
    }
}
