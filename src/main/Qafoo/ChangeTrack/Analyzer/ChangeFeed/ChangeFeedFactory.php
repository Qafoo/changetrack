<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeFeed;

use Qafoo\ChangeTrack\Analyzer\ChangeFeed;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\ChangeSetFactory;
use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

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
     * @param \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout $checkout
     * @param string $startRevision
     * @param string $endRevision
     * @return \Qafoo\Analyzer\ChangeFeed
     */
    public function createChangeFeed(GitCheckout $checkout, $startRevision = null, $endRevision = null)
    {
        return new ChangeFeed(
            $checkout,
            $this->changeSetFactory,
            $this->observer,
            $startRevision,
            $endRevision
        );
    }
}
