<?php

namespace App\Command\ValidationForm;

use App\Command\BaseCommand;
use App\Util\ConsoleCommandExecutor\ValidationForm\ValidationFormHelper;
use App\Util\Task\TaskResult;
use App\Util\ConsoleCommandExecutor\ValidationForm\ValidationFormExecutor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CompileCommand
 * @package App\Command\ValidationForm
 */
class CompileCommand extends BaseCommand
{
    const MODE_SHOW_STATUS = 'showStatusMode';
    const MODE_NEED_UPDATE = 'showNeedUpdateMode';
    const MODE_FORCE = 'forceMode';

    protected function configure()
    {
        $this
            ->setName('val-form:compile')
            ->addArgument('dirs',InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            // Mode only for show forms status
            ->addOption('state', 's')
            // Mode only for show forms status need to be updated
            ->addOption('update', 'u')
            // Mode for force update
            ->addOption('force', 'f')
            // Delete old and build again
            ->addOption('rebuild', 'r')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rebuild = $input->getOption('rebuild');
        if ($rebuild) {
            $path = ValidationFormHelper::getUtilCompiledPath();
            system('rm -rf ' . escapeshellarg($path));
        }

        $core = new ValidationFormExecutor();
        /** @var TaskResult $result */
        foreach ($core->compile($input->getArgument('dirs'), [
            self::MODE_SHOW_STATUS => $input->getOption('state'),
            self::MODE_NEED_UPDATE => $input->getOption('update'),
            self::MODE_FORCE => $input->getOption('force'),
        ]) as $result
        ) {
            $msg = (array)$result->getMessage();
            foreach ($msg as $row) {
                $output->writeln($row);
            }
        }

        $core->finalizeCompile();
        $output->writeln('Done');
        return 1;
    }
}
