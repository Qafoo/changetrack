<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;

class DiffIteratorFactory
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ReflectionLookup
     */
    private $reflectionLookup;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\ReflectionLookup $reflectionLookup
     */
    public function __construct(ReflectionLookup $reflectionLookup)
    {
        $this->reflectionLookup = $reflectionLookup;
    }

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
