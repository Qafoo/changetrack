<?php

namespace Qafoo\ChangeTrack\Analyzer\Checkout;

use Qafoo\ChangeTrack\Analyzer\Checkout;
use Arbit\VCSWrapper\GitCli\Checkout as ArbitCheckout;

class GitCheckout extends ArbitCheckout implements Checkout
{
    /**
     * @var string
     */
    private $currentRevision;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Checkout\VcsWrapperDiffMapper
     */
    private $diffMapper;

    /**
     * @param string $root
     * @param \Qafoo\ChangeTrack\Analyzer\Checkout\VcsWrapperDiffMapper $diffMapper
     */
    public function __construct($root, VcsWrapperDiffMapper $diffMapper)
    {
        parent::__construct($root);
        $this->diffMapper = $diffMapper;
    }

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
        return $this->diffMapper->mapDiffs(
            $parser->parseString($process->stdoutOutput)
        );
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
