<?php

namespace Qafoo\ChangeTrack\Commands;

use Qafoo\ChangeTrack\Analyzer;
use Qafoo\ChangeTrack\Analyzer\Renderer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Analyze extends Command
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer
     */
    private $analyzer;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Renderer
     */
    private $renderer;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer $analyzer
     * @param \Qafoo\ChangeTrack\Analyzer\Renderer $renderer
     * @param string $name
     */
    public function __construct(Analyzer $analyzer, Renderer $renderer, $name = null)
    {
        parent::__construct($name);
        $this->analyzer = $analyzer;
        $this->renderer = $renderer;
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        $checkoutPath = $input->getOption('checkout-path');
        $cachePath = $input->getOption('cache-path');

        $changes = $this->analyzer->analyze($url, $checkoutPath, $cachePath);

        $output->write($this->renderer->renderOutput($changes));
    }
}
