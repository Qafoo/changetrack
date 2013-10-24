<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Analyzer\AnalyzerFactory;
use Qafoo\ChangeTrack\Analyzer\Renderer;
use Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver\ProgressObserver;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Analyze extends BaseCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'analyze';
    }

    protected function configure()
    {
        $this->setName('analyze')
            ->setDescription('Analyze changes in a repository (currently only Git).')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'Repository URL'
            )->addOption(
                'working-path',
                'w',
                InputArgument::OPTIONAL,
                'Path to use for checkouts, temp files, etc. (must be empty dir)',
                'src/var/tmp'
            )->addOption(
                'start-revision',
                's',
                InputArgument::OPTIONAL,
                'Revision to start analyzis with.',
                null
            )->addOption(
                'end-revision',
                'e',
                InputArgument::OPTIONAL,
                'Revision to end analyzis with.',
                null
            )->addOption(
                'output',
                'o',
                InputArgument::OPTIONAL,
                'Output to given file instead of STDOUT.',
                null
            )->addOption(
                'progress',
                'p',
                InputOption::VALUE_NONE,
                'Display progress bar (requires output file to be specified).'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function configureContainer(InputInterface $input, OutputInterface $output)
    {
        $this->validateOptions($input);

        $this->getContainer()->setParameter(
            'Qafoo.ChangeTrack.Analyzer.WorkingPath',
            $input->getOption('working-path')
        );

        // TODO: Cleanup
        if ($input->getOption('progress')) {
            $this->getContainer()->set(
                'Qafoo.ChangeTrack.Analyzer.ChangeFeedObserver',
                new ProgressObserver(
                    $this->getHelperSet()->get('progress'),
                    $output
                )
            );
        }
    }

    private function validateOptions(InputInterface $input)
    {
        if ($input->getOption('progress') && $input->getOption('output') === null) {
            throw new \RuntimeException(
                'Progress can only be displayed when result is redirected to a file (--output).'
            );
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function executeCommand(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        $changes = $this->getContainer()->get('Qafoo.ChangeTrack.Analyzer')
            ->analyze(
                $url,
                $input->getOption('start-revision'),
                $input->getOption('end-revision')
            );

        $renderer = $this->getContainer()->get('Qafoo.ChangeTrack.Analyzer.Renderer');
        $resultOutput = $this->getContainer()->get('Qafoo.ChangeTrack.ResultOutputFactory')
            ->createOutputFromParameters($input, $output);

        $resultOutput->write($renderer->renderOutput($changes));
    }
}
