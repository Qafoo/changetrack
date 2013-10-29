<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\Change;

class SortingDiffIterator implements \IteratorAggregate
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\DiffIterator
     */
    private $innerIterator;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\DiffIterator $innerIterator
     */
    public function __construct(DiffIterator $innerIterator)
    {
        $this->innerIterator = $innerIterator;
    }

    /**
     * @var \Iterator(Qafoo\ChangeTrack\Analyzer\Change\LocalChange)
     */
    public function getIterator()
    {
        $innerChanges = iterator_to_array($this->innerIterator);

        usort(
            $innerChanges,
            function (Change\LocalChange $first, Change\LocalChange $second) {
                $firstLine = $first->getLineChange();
                $secondLine = $second->getLineChange();
                if ($firstLine instanceof Change\LineRemovedChange && $secondLine instanceof Change\LineAddedChange) {
                    return -1;
                }
                if ($secondLine instanceof Change\LineRemovedChange && $firstLine instanceof Change\LineAddedChange) {
                    return 1;
                }
                return strcmp($first->getFileChange()->getToFile(), $second->getFileChange()->getToFile());
            }
        );

        return new \ArrayIterator($innerChanges);
    }
}
