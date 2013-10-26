<?php

namespace Qafoo\ChangeTrack\Analyzer\Vcs;

class GitCheckout extends \Arbit\VCSWrapper\GitCli\Checkout
{
    /**
     * @var string
     */
    private $currentRevision;

    public function update($version = null)
    {
        if ($this->currentRevision === null || $this->currentRevision !== $version) {
            $this->currentRevision = $version;
            return parent::update($version);
        }
        return false;
    }

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

    /**
     * @param string $revision
     * @return bool
     */
    public function hasPredecessor($revision)
    {
        return strlen($this->getPredecessorCommitList($revision)) > 0;
    }

    /**
     * @param string $revision
     * @return string
     */
    private function getPredecessorCommitList($revision)
    {
        $process = new \Arbit\VCSWrapper\GitCli\Process();
        $process->workingDirectory($this->root);
        $process->argument('log')->argument('--pretty="format:%P"', true)
            ->argument('-1')->argument($revision)->execute();

        $result = trim($process->stdoutOutput);
        return $result;
    }

    /**
     * @param string $revision
     * @return string
     */
    public function getPredecessor($revision)
    {
        if (!$this->hasPredecessor($revision)) {
            throw new \RuntimeException(
                sprintf(
                    'No previous revision for "%s" found.',
                    $revision
                )
            );
        }

        $commitList = explode(' ', $this->getPredecessorCommitList($revision));
        return $commitList[0];
    }
}
