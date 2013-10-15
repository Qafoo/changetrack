<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;
use Qafoo\ChangeTrack\Analyzer\Change;

use Arbit\VCSWrapper\Diff;

class ChunkLineFeedGenerator extends LineChangeFeed
{
    /**
     * @var \Arbit\VCSWrapper\Diff\Chunk
     */
    private $diffChunk;

    /**
     * @var int
     */
    private $beforeLineIndex;

    /**
     * @var int
     */
    private $afterLineIndex;

    /**
     * @param \Arbit\VCSWrapper\Diff\Chunk $diffChunk
     */
    public function __construct(Diff\Chunk $diffChunk)
    {
        $this->diffChunk = $diffChunk;
    }

    public function getIterator()
    {
        $this->beforeLineIndex = $this->diffChunk->start;
        $this->afterLineIndex = $this->diffChunk->end;

        foreach ($this->diffChunk->lines as $line) {
            switch ($line->type) {
                case Diff\Line::ADDED:
                    yield new Change(
                        null,
                        $this->afterLineIndex,
                        Change::ADDED,
                        null,
                        null
                    );
                    $this->afterLineIndex++;
                    break;
                case Diff\Line::REMOVED:
                    yield new Change(
                        null,
                        $this->beforeLineIndex,
                        Change::REMOVED,
                        null,
                        null
                    );
                    $this->beforeLineIndex++;
                    break;
                case Diff\Line::UNCHANGED:
                    $this->beforeLineIndex++;
                    $this->afterLineIndex++;
                    break;
            }
        }

    }
}
