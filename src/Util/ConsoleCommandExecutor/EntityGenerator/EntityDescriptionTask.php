<?php


namespace App\Util\ConsoleCommandExecutor\EntityGenerator;

use App\Util\Task\Task;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer\DescriptionRenderer;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityRenderer\SchemaParser;

/**
 * Class EntityDescriptionTask
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator
 */
class EntityDescriptionTask extends Task
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
        $this->addTitle("* Task: entity description '<fg=magenta>{$entityName}</>' is creating");
    }

    /**
     * @return array|bool
     */
    protected function run()
    {
        $parser = new SchemaParser($this->entityName);
        $parser->run();
        if ($parser->hasErrors()) {
            return $parser->getMessages();
        }

        $renderer = new DescriptionRenderer($parser->getSchema());
        $renderer->run();
        return true;
    }
}