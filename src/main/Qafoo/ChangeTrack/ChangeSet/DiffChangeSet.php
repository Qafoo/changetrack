<?php

namespace Qafoo\ChangeTrack\ChangeSet;

use Qafoo\ChangeTrack\ChangeSet;
use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;
use Qafoo\ChangeTrack\Analyzer\LineFeed\ChunksLineFeedIterator;

use Arbit\VCSWrapper;

class DiffChangeSet extends ChangeSet
{
    private $checkout;

    private $revision;

    private $previousRevision;

    private $message;

    public function __construct(VCSWrapper\Checkout $checkout, $previousRevision, $revision, $message)
    {
        $this->checkout = $checkout;
        $this->previousRevision = $previousRevision;
        $this->revision = $revision;
        $this->message = $message;
    }

    public function recordChanges(ChangeRecorder $changeRecorder)
    {
        $this->checkout->update($this->revision);

        $diffIterator = new Diff\DiffIterator(
            $this->checkout->getDiff(
                $this->previousRevision,
                $this->revision
            )
        );

        foreach ($diffIterator as $diffCollection) {
            $chunksIterator = new Diff\LineChangeFeed\ChunksLineFeedIterator($diffCollection->chunks);

            if ($diffCollection->to === '/dev/null') {
                // File has been deleted.
                continue;
            }

            foreach ($chunksIterator as $change) {
                $change->localFile = $this->checkout->getLocalPath() . substr($diffCollection->to, 1);
                $change->revision = $this->revision;
                $change->message = $this->message;

                $changeRecorder->recordChange($change);
            }
        }
    }
}
