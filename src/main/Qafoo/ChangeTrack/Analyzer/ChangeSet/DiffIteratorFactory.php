<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;

class DiffIteratorFactory
{
    /**
     * @param array $diffs
     * @return \Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\DiffIterator
     */
    public function createDiffIterator(array $diffs)
    {
        return new Diff\SortingDiffIterator(
            new Diff\DiffIterator(
                $diffs
            )
        );
    }
}
