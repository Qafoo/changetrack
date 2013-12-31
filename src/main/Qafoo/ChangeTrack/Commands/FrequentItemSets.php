<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Calculator;
use Qafoo\ChangeTrack\Calculator\Parser;
use Qafoo\ChangeTrack\Calculator\Renderer;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FrequentItemSets extends BaseCommand
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
        return 'frequent-item-sets';
    }

    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription('Calculate frequent item sets on a given analysis result.');

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
        $inputXml = $this->inputFileParameterFactory->getInputFromParameters($input);

        $parser = $this->getContainer()->get('Qafoo.ChangeTrack.Parser');
        $calculator = $this->getContainer()->get('Qafoo.ChangeTrack.FISCalculator');
        $renderer = $this->getContainer()->get('Qafoo.ChangeTrack.FISCalculator.Renderer');

        $analysisResult = $parser->parseAnalysisResult($inputXml);

        $itemSets = $calculator->calculateFrequentItemSets($analysisResult, 0.5);

        $output->write($renderer->renderOutput($itemSets));
    }
}
