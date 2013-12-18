<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\PathFilter;

class DiffIteratorFactory
{
    /**
     * @param array $diffs
     * @return \Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\DiffIterator
     */
    public function createDiffIterator(array $diffs, array $paths = array(), array $excludedPaths = array())
    {
        return new Diff\SortingDiffIterator(
            new Diff\FilteringDiffIterator(
                new Diff\DiffIterator(
                    $diffs
                ),
                new PathFilter(
                    $paths,
                    $excludedPaths
                )
            )
        );
    }
}
