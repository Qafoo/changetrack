<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;

class Application extends BaseApplication
{
    /**
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->getDefinition()->addOption(
            new InputOption(
                '--config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                'Configuration file.'
            )
        );
    }
}
