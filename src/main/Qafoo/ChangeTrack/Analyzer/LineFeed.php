<?php

namespace Qafoo\ChangeTrack\Analyzer;

abstract class LineFeed implements \IteratorAggregate
{
    /**
     * Returns an Iterator over line numbers
     *
     * @return Iterator<int>
     */
    abstract public function getIterator();
}
