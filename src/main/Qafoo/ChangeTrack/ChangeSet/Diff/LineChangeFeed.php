<?php

namespace Qafoo\ChangeTrack\ChangeSet\Diff;

abstract class LineChangeFeed implements \IteratorAggregate
{
    /**
     * Returns an Iterator over line changes
     *
     * @return Iterator<Change>
     */
    abstract public function getIterator();
}
