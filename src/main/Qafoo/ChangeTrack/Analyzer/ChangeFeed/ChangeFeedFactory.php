<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeFeed;

use Qafoo\ChangeTrack\Analyzer\RevisionBoundaries;
use Qafoo\ChangeTrack\Analyzer\ChangeFeed;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\ChangeSetFactory;
use Qafoo\ChangeTrack\Analyzer\Checkout;

class ChangeFeedFactory
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeFeedObserver
     */
    private $observer;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeSet\ChangeSetFactory
     */
    private $changeSetFactory;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeFeedObserver $observer
     */
    public function __construct(ChangeFeedObserver $observer, ChangeSetFactory $changeSetFactory)
    {
        $this->observer = $observer;
        $this->changeSetFactory = $changeSetFactory;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Checkout $checkout
     * @param \Qafoo\ChangeTrack\Analyzer\RevisionBoundaries $boundaries
     * @return \Qafoo\Analyzer\ChangeFeed
     */
    public function createChangeFeed(Checkout $checkout, RevisionBoundaries $boundaries)
    {
        return new ChangeFeed(
            $checkout,
            $this->changeSetFactory,
            $this->observer,
            $boundaries
        );
    }
}
