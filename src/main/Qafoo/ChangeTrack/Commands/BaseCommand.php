<?php

namespace Qafoo\ChangeTrack\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class BaseCommand extends Command
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        parent::__construct($this->getCommandName());
        $this->container = $container;
    }

    /**
     * @return string
     */
    abstract protected function getCommandName();

    /**
     * Prepare the container according to the provided config flags.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    abstract protected function configureContainer(InputInterface $input, OutputInterface $output);

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    abstract protected function executeCommand(InputInterface $input, OutputInterface $output);

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configureContainer($input, $output);
        $this->container->compile();
        $this->executeCommand($input, $output);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
