<?php

namespace App\Command\ValidationForm;

use App\Command\BaseCommand;
use App\Util\Task\TaskResult;
use App\Util\ConsoleCommandExecutor\ValidationForm\ValidationFormExecutor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RenewForControllerCommand
 * @package App\Command\ValidationForm
 */
class RenewForControllerCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('val-form:renew-for-controller')
            ->addArgument('controllers',InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $core = new ValidationFormExecutor();
        /** @var TaskResult $result */
        foreach ($core->renewUtilForController($input->getArgument('controllers')) as $result) {
            $msg = (array)$result->getMessage();
            foreach ($msg as $row) {
                $output->writeln($row);
            }
        }

        $output->writeln('Done');
        return 1;
    }
}
