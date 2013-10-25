<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class LocalChange
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Change\FileChange
     */
    private $fileChange;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Change\LineChange
     */
    private $lineChange;

    public function __construct(FileChange $fileChange, LineChange $lineChange)
    {
        $this->fileChange = $fileChange;
        $this->lineChange = $lineChange;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout $checkout
     */
    public function determineAffectedArtifact(GitCheckout $checkout, $revision)
    {
        return $this->lineChange->determineAffectedArtifact(
            $checkout,
            $revision,
            $this->fileChange
        );
    }

    public function getLineChange()
    {
        return $this->lineChange;
    }
}
