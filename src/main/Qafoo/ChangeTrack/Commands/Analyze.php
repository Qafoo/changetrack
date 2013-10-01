<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Analyzer\AnalyzerFactory;
use Qafoo\ChangeTrack\Analyzer\Renderer;

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
                'checkout-path',
                'co',
                InputArgument::OPTIONAL,
                'Path to use for checkouts (must be empty dir)',
                'src/var/tmp/checkout'
            )->addOption(
                'cache-path',
                'ca',
                InputArgument::OPTIONAL,
                'Path to use for meta data cache',
                'src/var/tmp/cache'
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
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     */
    protected function configureContainer(InputInterface $input)
    {
        $this->getContainer()->setParameter(
            'Qafoo.ChangeTrack.Analyzer.CheckoutPath',
            $input->getOption('checkout-path')
        );
        $this->getContainer()->setParameter(
            'Qafoo.ChangeTrack.Analyzer.CachePath',
            $input->getOption('cache-path')
        );
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

        $output->write($renderer->renderOutput($changes));
    }
}
