<?php

namespace App\Command\Entity;

use App\Command\BaseCommand;
use App\Util\Task\TaskResult;
use App\Util\ConsoleCommandExecutor\EntityGenerator\EntityGeneratorExecutor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EntityGenerateCommand
 * @package App\Command\Entity
 */
class GenerateCommand extends BaseCommand
{
    const MODE_SHOW_STATUS = 'showStatusMode';
    const MODE_NEED_UPDATE = 'showNeedUpdateMode';

    protected function configure()
    {
        $this
            ->setName('entity:generate')
            ->addArgument('entities',InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            // Mode only for show entities status
            ->addOption('state', 's')
            // Mode only for show entities status need to be updated
            ->addOption('update', 'u')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = new EntityGeneratorExecutor();
        /** @var TaskResult $result */
        foreach ($generator->generatePhp($input->getArgument('entities'), [
                self::MODE_SHOW_STATUS => $input->getOption('state'),
                self::MODE_NEED_UPDATE => $input->getOption('update'),
            ]) as $result
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
