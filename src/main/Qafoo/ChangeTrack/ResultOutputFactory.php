<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class ResultOutputFactory
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $defaultOutput
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function createOutputFromParameters(InputInterface $input, OutputInterface $defaultOutput)
    {
        if ($input->getOption('output') !== null) {
            return new StreamOutput(
                fopen($input->getOption('output'), 'w')
            );
        }
        return $defaultOutput;
    }
}
