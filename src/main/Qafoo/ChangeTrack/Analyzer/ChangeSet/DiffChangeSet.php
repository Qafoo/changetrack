<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\Change;
use Qafoo\ChangeTrack\Analyzer\ChangeSet;
use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;
use Qafoo\ChangeTrack\Analyzer\LineFeed\ChunksLineFeedIterator;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class DiffChangeSet extends ChangeSet
{
    private $beforeCheckout;

    private $revision;

    private $previousRevision;

    private $message;

    public function __construct(GitCheckout $beforeCheckout, GitCheckout $afterCheckout, $revision, $message)
    {
        $this->beforeCheckout = $beforeCheckout;
        $this->afterCheckout = $afterCheckout;
        $this->revision = $revision;
        $this->message = $message;
    }

    public function recordChanges(ChangeRecorder $changeRecorder)
    {
        $this->afterCheckout->update($this->revision);

        $diffIterator = new Diff\DiffIterator(
            $this->afterCheckout->getRevisionDiff($this->revision),
            $this->beforeCheckout->getLocalPath(),
            $this->afterCheckout->getLocalPath()
        );

        if ($this->afterCheckout->hasPredecessor($this->revision)) {
            $this->beforeCheckout->update(
                $this->afterCheckout->getPredecessor($this->revision)
            );
            // TODO: Ensure that no removals occur if no predecessor exists!
        }

        foreach ($diffIterator as $change) {

            $change->revision = $this->revision;
            $change->message = $this->message;

            $changeRecorder->recordChange($change);
        }
    }
}
