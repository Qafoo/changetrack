<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Calculator;
use Qafoo\ChangeTrack\Calculator\Parser;
use Qafoo\ChangeTrack\Calculator\Renderer;

use Qafoo\ChangeTrack\FISCalculator\TransactionDatabase;
use Qafoo\ChangeTrack\FISCalculator\MethodTransactionDatabaseFactory;

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
            ->setDescription('Calculate frequent item sets on a given analysis result.')
            ->addOption(
                'min-support',
                's',
                InputOption::VALUE_REQUIRED,
                'Minimum support for an item set to be considered frequent.',
                0.5
            )->addOption(
                'item-type',
                'i',
                InputOption::VALUE_REQUIRED,
                'Artifact type which should be used as items (method/class).',
                'method'
            );

        $this->inputFileParameterFactory->registerParameters($this);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function configureContainer(InputInterface $input, OutputInterface $output)
    {
        $this->validateOptions($input);
    }

    private function validateOptions(InputInterface $input)
    {
        if (!is_numeric($input->getOption('min-support'))) {
            throw new \InvalidArgumentException(
                'Option "min-support" must be numeric'
            );
        }

        $minSupport = (float) $input->getOption('min-support');
        if ($minSupport < 0 || $minSupport > 1) {
            throw new \InvalidArgumentException(
                'Option "min-support" must not be between 0 and 1'
            );
        }
    }

    protected function executeCommand(InputInterface $input, OutputInterface $output)
    {
        $inputXml = $this->inputFileParameterFactory->getInputFromParameters($input);

        $parser = $this->getContainer()->get('Qafoo.ChangeTrack.Parser');
        $calculator = $this->getContainer()->get('Qafoo.ChangeTrack.FISCalculator');
        $renderer = $this->getContainer()->get('Qafoo.ChangeTrack.FISCalculator.Renderer');

        $analysisResult = $parser->parseAnalysisResult($inputXml);

        $databaseFactory = $this->getContainer()->get(
            'Qafoo.ChangeTrack.FISCalculator.TransactionDatabaseFactoryLocator'
        )->getFactoryByType(
            $input->getOption('item-type')
        );
        $transactionBase = $databaseFactory->createDatabase($analysisResult);

        $itemSets = $calculator->calculateFrequentItemSets(
            $transactionBase,
            (float) $input->getOption('min-support')
        );

        $output->write($renderer->renderOutput($itemSets));
    }
}
