<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Analyzer;
use Qafoo\ChangeTrack\Renderer\JmsSerializerRenderer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Track extends Command
{
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        $checkoutPath = $input->getOption('checkout-path');
        $cachePath = $input->getOption('cache-path');

        $analyzer = new Analyzer($url, $checkoutPath, $cachePath);
        $changes = $analyzer->analyze();

        $renderer = new JmsSerializerRenderer();

        $output->write($renderer->renderOutput($changes));
    }
}
