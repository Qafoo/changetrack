<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\Change;

class DiffIterator implements \IteratorAggregate
{
    /**
     * @var \Arbit\VCSWrapper\Diff\Collection[]
     */
    private $diffs;

    /**
     * @var string
     */
    private $beforePath;

    /**
     * @var string
     */
    private $afterPath;

    /**
     * @param \Arbit\VCSWrapper\Diff\Collection[] $diffs
     */
    public function __construct(array $diffs, $beforePath, $afterPath)
    {
        $this->diffs = $diffs;
        $this->beforePath = $beforePath;
        $this->afterPath = $afterPath;
    }

    /**
     * @var \Iterator(Qafoo\ChangeTrack\Analyzer\Change)
     */
    public function getIterator()
    {
        foreach ($this->diffs as $diffCollection) {
            $chunksIterator = new LineChangeFeed\ChunksLineFeedIterator($diffCollection->chunks);

            foreach ($chunksIterator as $change) {
                // FIXME: Refactor!
                switch($change->changeType) {
                    case Change::REMOVED:
                        $change->localFile = $this->beforePath . substr($diffCollection->from, 1);
                        break;
                    case Change::ADDED:
                        $change->localFile = $this->afterPath . substr($diffCollection->to, 1);
                        break;
                }
                yield $change;
            }
        }
    }
}
