<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\InitialChangeSet;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffChangeSet;
use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class ChangeFeed implements \Iterator
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout
     */
    private $checkout;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeFeedObserver
     */
    private $observer;

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

    public function __construct(
        GitCheckout $checkout,
        ChangeFeedObserver $observer,
        $startRevision = null,
        $endRevision = null
    ) {
        $this->checkout = $checkout;
        $this->observer = $observer;
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
        $this->observer->notifyInitialized($this->revisions);
    }

    public function next()
    {
        $this->revisionIndex++;
    }

    public function current()
    {
        $currentRevision = $this->getCurrentRevision();
        $this->checkout->update($currentRevision);

        $this->observer->notifyProcessingRevision(
            $this->revisionIndex,
            $currentRevision
        );

        return new DiffChangeSet(
            $this->checkout,
            $currentRevision,
            $this->checkout->getLogEntry($currentRevision)->message
        );
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
