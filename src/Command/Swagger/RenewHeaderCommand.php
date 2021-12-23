<?php

namespace App\Command\Swagger;

use App\Command\BaseCommand;
use App\Util\ConsoleCommandExecutor\Swagger\SwaggerExecutor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SwaggerRenewHeader
 * @package App\Command\Swagger
 */
class RenewHeaderCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('swagger:renew-header')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $core = new SwaggerExecutor();
        $core->renewHeader();
        $output->writeln('Done');
        return 1;
    }
}
