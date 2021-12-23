<?php

// src/Command/TestCommand.php
namespace App\Command;

use App\Kernel;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Command
 */
class BaseCommand extends Command
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('base:run')
            ->setDescription('Just a base command.')
            ->setHelp('Really this is just a base command...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Base command is OK');
        return 1;
    }

    /**
     * @return Kernel
     */
    protected function getApp()
    {
        return $this->container->get('kernel');
    }

    /**
     * @return string
     */
    protected function getProjectDir()
    {
        return $this->getApp()->getProjectDir();
    }
}
