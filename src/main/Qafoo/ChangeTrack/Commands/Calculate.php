<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Calculator;
use Qafoo\ChangeTrack\Parser;
use Qafoo\ChangeTrack\Calculator\Renderer;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Calculate extends BaseCommand
{
    /**
     * @var \Qafoo\ChangeTrack\Commands\InputFileParameterFactory
     */
    private $inputFileParameterFactory;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->inputFileParameterFactory = new InputFileParameterFactory();
        parent::__construct($container);
    }

    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'calculate';
    }

    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription('Calculate stats on a given analysis result.');

        $this->inputFileParameterFactory->registerParameters($this);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function configureContainer(InputInterface $input, OutputInterface $output)
    {
        // TODO: Implement
    }

    protected function executeCommand(InputInterface $input, OutputInterface $output)
    {

        $parser = $this->getContainer()->get('Qafoo.ChangeTrack.Parser');
        $calculator = $this->getContainer()->get('Qafoo.ChangeTrack.Calculator');
        $renderer = $this->getContainer()->get('Qafoo.ChangeTrack.Calculator.Renderer');

        $inputXml = $this->inputFileParameterFactory->getInputFromParameters($input);

        $analysisResult = $parser->parseAnalysisResult($inputXml);

        $stats = $calculator->calculateStats($analysisResult);

        $output->write($renderer->renderOutput($stats));
    }
}
