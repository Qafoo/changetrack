<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Calculator;
use Qafoo\ChangeTrack\Calculator\Parser\JmsSerializerParser;
use Qafoo\ChangeTrack\Calculator\Renderer\JmsSerializerRenderer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Calculate extends Command
{
    /**
     * @param \Qafoo\ChangeTrack\Calculator $calculator
     * @param \Qafoo\ChangeTrack\Calculator\Renderer $renderer
     * @param string $name
     */
    public function __construct(Calculator $calculator, $name = null)
    {
        parent::__construct($name);
        $this->calculator = $calculator;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = 'php://stdin';
        if ($input->hasArgument('file')) {
            $inputFile = $input->getArgument('file');

            if (!file_exists($inputFile)) {
                throw new \RuntimeException('File not found: "' . $inputFile . '"');
            }
        }
        $inputXml = file_get_contents($inputFile);

        $parser = new JmsSerializerParser();
        $analysisResult = $parser->parseAnalysisResult($inputXml);

        $stats = $this->calculator->calculateStats($analysisResult);

        $renderer = new JmsSerializerRenderer();

        $output->write($renderer->renderOutput($stats));
    }
}
