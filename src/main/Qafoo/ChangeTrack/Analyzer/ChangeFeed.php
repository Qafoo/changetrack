<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\InitialChangeSet;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffChangeSet;

class ChangeFeed implements \Iterator
{
    private $checkout;

    private $revisions;

    private $revisionIndex;

    public function __construct($checkout)
    {
        $this->checkout = $checkout;
        $this->revisions = $this->checkout->getVersions();
    }

    public function valid()
    {
        return $this->revisionIndex < count($this->revisions);
    }

    public function rewind()
    {
        $this->revisionIndex = 0;
    }

    public function next()
    {
        $this->revisionIndex++;
    }

    public function current()
    {
        $currentRevision = $this->revisions[$this->revisionIndex];
        $this->checkout->update($currentRevision);

        if ($this->revisionIndex == 0) {
            return new InitialChangeSet(
                $this->checkout,
                $currentRevision,
                $this->checkout->getLogEntry($currentRevision)->message
            );
        } else {
            $previousRevision = $this->revisions[$this->revisionIndex - 1];

            return new DiffChangeSet(
                $this->checkout,
                $previousRevision,
                $currentRevision,
                $this->checkout->getLogEntry($currentRevision)->message
            );
        }
    }

    public function key()
    {
        return $this->revisions[$this->revisionIndex];
    }
}
