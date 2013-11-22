<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;
use Qafoo\ChangeTrack\Analyzer\Change\LineAddedChange;
use Qafoo\ChangeTrack\Analyzer\Change\LineRemovedChange;

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

    /**
     * @return \Iterator(\Qafoo\ChangeTrack\Analyzer\Change\LineChange)
     */
    public function getIterator()
    {
        $this->beforeLineIndex = $this->diffChunk->start;
        $this->afterLineIndex = $this->diffChunk->end;

        foreach ($this->diffChunk->lines as $line) {
            switch ($line->type) {
                case Diff\Line::ADDED:
                    yield new LineAddedChange($this->afterLineIndex);
                    $this->afterLineIndex++;
                    break;
                case Diff\Line::REMOVED:
                    yield new LineRemovedChange($this->beforeLineIndex);
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
