<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\ChangeSet;
use Qafoo\ChangeTrack\Analyzer\Change;
use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;
use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class DiffChangeSet extends ChangeSet
{
    private $checkout;

    private $revision;

    private $message;

    public function __construct(GitCheckout $checkout, $revision, $message)
    {
        $this->checkout = $checkout;
        $this->revision = $revision;
        $this->message = $message;
    }

    public function recordChanges(ChangeRecorder $changeRecorder)
    {
        $diffIterator = new Diff\SortingDiffIterator(
            new Diff\DiffIterator(
                $this->checkout->getRevisionDiff($this->revision)
            )
        );

        foreach ($diffIterator as $localChange) {

            $change = new Change($localChange, $this->revision, $this->message);
            $changeRecorder->recordChange($change, $this->checkout, $this->checkout);
        }
    }
}
