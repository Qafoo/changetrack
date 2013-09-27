<?php

namespace Qafoo\ChangeTrack\Analyzer\Vcs;

class GitCheckout extends \Arbit\VCSWrapper\GitCli\Checkout
{
    /**
     * Returns the diff that was applied in the given $revision
     *
     * @param string $revision
     * @return array(\Arbit\VCSWrapper\Diff\Collection)
     */
    public function getRevisionDiff($revision)
    {
        $process = new \Arbit\VCSWrapper\GitCli\Process();
        $process->workingDirectory($this->root);
        $process->argument('show')->argument('--pretty="format:%b"')
            ->argument($revision)->execute();

        $parser = new \Arbit\VCSWrapper\Diff\Unified();
        return $parser->parseString($process->stdoutOutput);
    }
}
