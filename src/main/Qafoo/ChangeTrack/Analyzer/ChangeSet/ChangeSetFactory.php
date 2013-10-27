<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class ChangeSetFactory
{
    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout $checkout
     * @param string $revision
     * @param string $message
     * @return \Qafoo\ChangeTrack\Analyzer\ChangeSet\DiffChangeSet
     */
    public function createDiffChangeSet(GitCheckout $checkout, $revision, $message)
    {
        return new DiffChangeSet($checkout, $revision, $message);
    }
}
