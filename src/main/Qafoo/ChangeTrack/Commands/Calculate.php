<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Calculator;
use Qafoo\ChangeTrack\Calculator\Parser;
use Qafoo\ChangeTrack\Calculator\Renderer;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Calculate extends BaseCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'calculate';
    }

    protected function configure()
    {
        $this->setName('calculate')
            ->setDescription('Calculate stats on a given analysis result.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'File to read analysis result from. If not given, STDIN is used.'
            );
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
        $inputFile = 'php://stdin';
        if ($input->hasArgument('file')) {
            $inputFile = $input->getArgument('file');

            if (!file_exists($inputFile)) {
                throw new \RuntimeException('File not found: "' . $inputFile . '"');
            }
        }
        $inputXml = file_get_contents($inputFile);

        $parser = $this->getContainer()->get('Qafoo.ChangeTrack.Calculator.Parser');
        $calculator = $this->getContainer()->get('Qafoo.ChangeTrack.Calculator');
        $renderer = $this->getContainer()->get('Qafoo.ChangeTrack.Calculator.Renderer');

        $analysisResult = $parser->parseAnalysisResult($inputXml);

        $stats = $calculator->calculateStats($analysisResult);

        $output->write($renderer->renderOutput($stats));
    }
}
