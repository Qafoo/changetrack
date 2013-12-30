<?php

namespace Qafoo\ChangeTrack\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;

class InputFileParameterFactory
{
    /**
     * @param \Symfony\Console\Command\Command $command
     */
    public function registerParameters(Command $command)
    {
        $command->addArgument(
            'file',
            InputArgument::OPTIONAL,
            'File to read analysis result from. If not given, STDIN is used.'
        );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return string
     */
    public function getInputFromParameters(InputInterface $input)
    {
        $inputFile = 'php://stdin';
        if ($input->hasArgument('file')) {
            $inputFile = $input->getArgument('file');

            if (!file_exists($inputFile)) {
                throw new \RuntimeException('File not found: "' . $inputFile . '"');
            }
        }
        return file_get_contents($inputFile);
    }
}
