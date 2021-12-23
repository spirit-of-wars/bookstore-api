<?php

namespace App\Util\ConsoleCommandExecutor\EntityGenerator;

use App\Command\Entity\GenerateCommand;
use App\Mif;

/**
 * Class ValidationTask
 * @package App\Util\ConsoleCommandExecutor\EntityGenerator
 */
class ValidationTask extends AbstractValidationTask
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

        $targetFileName = Mif::getProjectDir() . EntityGeneratorHelper::getEntityPath($name);
        $needUpdate = true;
        if (file_exists($targetFileName)) {
            $needUpdate = (
                filemtime($targetFileName) < filemtime($specFileName)
            );
        }

        if ($needUpdate) {
            $showStatusMode = $this->getContext()->getData(GenerateCommand::MODE_SHOW_STATUS);
            if ($showStatusMode) {
                $this->addTextRow($row . '<fg=yellow>need update</>');
            } else {
                $this->addTextRow($row . '<fg=yellow>need update. I\'m creating a new task...</>');
                $this->addSubTask($name, new EntityTask($name));
            }
        } else {
            $showNeedUpdateMode = $this->getContext()->getData(GenerateCommand::MODE_NEED_UPDATE);
            if (!$showNeedUpdateMode) {
                $this->addTextRow($row . '<fg=green>current state is actual</>');
            }
        }
    }
}
