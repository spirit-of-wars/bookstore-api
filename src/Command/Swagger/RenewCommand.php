<?php

namespace App\Command\Swagger;

use App\Command\BaseCommand;
use App\Util\ConsoleCommandExecutor\Swagger\SwaggerExecutor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RenewCommand
 * @package App\Command\Swagger
 */
class RenewCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('swagger:renew')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $core = new SwaggerExecutor();
        foreach ($core->renew() as $result) {
            $msg = (array)$result->getMessage();
            foreach ($msg as $row) {
                $output->writeln($row);
            }
        }

        $output->writeln('Done');
        return 1;
    }
}
