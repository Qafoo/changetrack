<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\RevisionBoundaries;
use Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\InitialChangeSet;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\ChangeSetFactory;
use Qafoo\ChangeTrack\Analyzer\Checkout;

class ChangeFeed implements \Iterator
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeSet\ChangeSetFactory
     */
    private $changeSetFactory;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Checkout
     */
    private $checkout;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver
     */
    private $observer;

    /**
     * @var string[]
     */
    private $revisions;

    /**
     * @var int
     */
    private $revisionIndex;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $endIndex;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Checkout $checkout
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeSet\ChangeSetFactory $changeSetFactory
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver $observer
     * @param \Qafoo\ChangeTrack\Analyzer\RevisionBoundaries $revisionBoundaries
     */
    public function __construct(
        Checkout $checkout,
        ChangeSetFactory $changeSetFactory,
        ChangeFeedObserver $observer,
        RevisionBoundaries $revisionBoundaries
    ) {
        $this->checkout = $checkout;
        $this->changeSetFactory = $changeSetFactory;
        $this->observer = $observer;
        $this->revisions = $this->checkout->getVersions();

        $this->determineEdgeIndexes($revisionBoundaries);
        $this->rewind();
    }

    /**
     * @param string $startRevision
     * @param string $endRevision
     */
    private function determineEdgeIndexes(RevisionBoundaries $boundaries)
    {
        $startIndex = 0;
        $endIndex = count($this->revisions) - 1;

        foreach ($this->revisions as $index => $revision) {
            if ($boundaries->startNotNullAndEquals($revision)) {
                $startIndex = $index;
            }
            if ($boundaries->endNotNullAndEquals($revision)) {
                $endIndex = $index;
            }
        }

        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
    }

    /**
     * @return bool
     */
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

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\ChangeSet
     */
    public function current()
    {
        $currentRevision = $this->getCurrentRevision();
        $this->checkout->update($currentRevision);

        $this->observer->notifyProcessingRevision(
            $this->revisionIndex,
            $currentRevision
        );

        return $this->changeSetFactory->createDiffChangeSet(
            $this->checkout,
            $currentRevision,
            $this->checkout->getLogEntry($currentRevision)->message
        );
    }

    /**
     * @return string
     */
    private function getCurrentRevision()
    {
        return $this->revisions[$this->revisionIndex];
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->getCurrentRevision();
    }
}
