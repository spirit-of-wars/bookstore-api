<?php

namespace App\Command\Entity;

use App\Command\BaseCommand;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityGeneratorExecutor;
use App\Util\Task\TaskResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EntityMakeDescriptionCommand
 * @package App\Command\Entity
 */
class MakeDescriptionCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('entity:make-description')
            ->addArgument('entities',InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = new EntityGeneratorExecutor();
        /** @var TaskResult $result */
        foreach ($generator->compile($input->getArgument('entities')) as $result
        ) {
            $msg = (array)$result->getMessage();
            foreach ($msg as $row) {
                $output->writeln($row);
            }
        }

        $output->writeln('Done');
        return 1;
    }
}
