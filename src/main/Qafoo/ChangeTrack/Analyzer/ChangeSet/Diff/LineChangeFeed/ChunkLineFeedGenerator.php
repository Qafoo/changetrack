<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

// TODO: Cleanup namespaces
use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;
use Qafoo\ChangeTrack\Analyzer\Change\LineAddedChange;
use Qafoo\ChangeTrack\Analyzer\Change\LineRemovedChange;
use Qafoo\ChangeTrack\Analyzer\Diff\Chunk;
use Qafoo\ChangeTrack\Analyzer\Diff\Line;

class ChunkLineFeedGenerator extends LineChangeFeed
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Diff\Chunk
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
     * @param \Qafoo\ChangeTrack\Analyzer\Diff\Chunk $diffChunk
     */
    public function __construct(Chunk $diffChunk)
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
                case Line::ADDED:
                    yield new LineAddedChange($this->afterLineIndex);
                    $this->afterLineIndex++;
                    break;
                case Line::REMOVED:
                    yield new LineRemovedChange($this->beforeLineIndex);
                    $this->beforeLineIndex++;
                    break;
                case Line::UNCHANGED:
                    $this->beforeLineIndex++;
                    $this->afterLineIndex++;
                    break;
            }
        }
    }
}
