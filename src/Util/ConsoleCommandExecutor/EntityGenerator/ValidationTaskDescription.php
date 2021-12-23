<?php


namespace App\Util\ConsoleCommandExecutor\EntityGenerator;

use App\Mif;

/**
 * Class ValidationTaskDescription
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator
 */
class ValidationTaskDescription extends AbstractValidationTask
{
    /**
     * @param string $name
     */
    protected function validateEntityName($name)
    {
        $specFileName = Mif::getProjectDir() . EntityGeneratorHelper::getYamlEntityPath($name);
        $row = "- Entity '<fg=magenta>$name</>': ";
        if (!file_exists($specFileName)) {
            $this->addTextRow($row . '<fg=red>specification not found</>');
            return;
        }

        $targetFileName = Mif::getProjectDir() . EntityGeneratorHelper::getDescriptionEntityPath($name);
        if (file_exists($targetFileName)){
            $this->addTextRow($row . '<fg=green>description is actual</>');
           return;
        }

        $this->addTextRow($row . '<fg=yellow>need create description. I\'m creating a new task...</>');
        $this->addSubTask($name, new EntityDescriptionTask($name));
    }
}
