<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class ChangeSetFactory
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffIteratorFactory
     */
    private $diffIteratorFactory;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffIteratorFactory
     */
    public function __construct(DiffIteratorFactory $diffIteratorFactory)
    {
        $this->diffIteratorFactory = $diffIteratorFactory;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout $checkout
     * @param string $revision
     * @param string $message
     * @return \Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffChangeSet
     */
    public function createDiffChangeSet(GitCheckout $checkout, $revision, $message)
    {
        return new DiffChangeSet($checkout, $this->diffIteratorFactory, $revision, $message);
    }
}
