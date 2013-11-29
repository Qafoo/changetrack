<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;
use Qafoo\ChangeTrack\Analyzer\Change\FileChange;
use Qafoo\ChangeTrack\Analyzer\Change\LocalChange;

class DiffIterator implements \IteratorAggregate
{
    /**
     * @var \Arbit\VCSWrapper\Diff\Collection[]
     */
    private $diffs;

    /**
     * @param \Arbit\VCSWrapper\Diff\Collection[] $diffs
     */
    public function __construct(array $diffs)
    {
        $this->diffs = $diffs;
    }

    /**
     * @var \Iterator(Qafoo\ChangeTrack\Analyzer\Change\LocalChange)
     */
    public function getIterator()
    {
        foreach ($this->diffs as $diffCollection) {
            if (substr($diffCollection->from, -3, 3) !== 'php' && substr($diffCollection->to, -3, 3) !== 'php') {
                continue;
            }

            $chunksIterator = new LineChangeFeed\ChunksLineFeedIterator(
                $diffCollection->chunks
            );

            $fileChange = new FileChange(
                substr($diffCollection->from, 1),
                substr($diffCollection->to, 1)
            );

            foreach ($chunksIterator as $lineChange) {
                $localChange = new LocalChange($fileChange, $lineChange);
                yield $localChange;
            }
        }
    }
}
