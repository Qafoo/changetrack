<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\InitialChangeSet;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffChangeSet;

class ChangeFeed implements \Iterator
{
    private $checkout;

    private $revisions;

    private $revisionIndex;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $endIndex;

    public function __construct($checkout, $startRevision = null, $endRevision = null)
    {
        $this->checkout = $checkout;
        $this->revisions = $this->checkout->getVersions();

        $this->determineEdgeIndexes($startRevision, $endRevision);
        $this->rewind();
    }

    /**
     * @param string $startRevision
     * @param string $endRevision
     */
    private function determineEdgeIndexes($startRevision, $endRevision)
    {
        $startIndex = 0;
        $endIndex = count($this->revisions) - 1;

        foreach ($this->revisions as $index => $revision) {
            if ($startRevision !== null && $revision === $startRevision) {
                $startIndex = $index;
            }
            if ($endRevision !== null && $revision === $endRevision) {
                $endIndex = $index;
            }
        }

        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
    }

    public function valid()
    {
        return $this->revisionIndex <= $this->endIndex;
    }

    public function rewind()
    {
        $this->revisionIndex = $this->startIndex;
    }

    public function next()
    {
        $this->revisionIndex++;
    }

    public function current()
    {
        $currentRevision = $this->getCurrentRevision();
        $this->checkout->update($currentRevision);

        if ($this->revisionIndex == $this->startIndex) {
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

    private function getCurrentRevision()
    {
        return $this->revisions[$this->revisionIndex];
    }

    public function key()
    {
        return $this->getCurrentRevision();
    }
}
