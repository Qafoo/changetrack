<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;
use Qafoo\ChangeTrack\Analyzer\Change;

use Arbit\VCSWrapper\Diff;

class ChunkLineFeedGenerator extends LineChangeFeed
{
    private $chunk;

    public function __construct(Diff\Chunk $chunk)
    {
        $this->chunk = $chunk;
    }

    public function getIterator()
    {
        $lineCount = count($this->chunk->lines);
        $lineNumber = $this->chunk->end;

        for ($lineOffset = 0; $lineOffset < $lineCount; $lineOffset++) {
            $line = $this->chunk->lines[$lineOffset];

            switch ($line->type) {
                case Diff\Line::ADDED:
                    yield new Change(null, $lineNumber, Change::ADDED, null, null);
                    $lineNumber++;
                    break;
                case Diff\Line::UNCHANGED:
                    $lineNumber++;
                    break;
                case Diff\Line::REMOVED:
                    yield new Change(null, $lineNumber, Change::REMOVED, null, null);
                    break;
            }
        }
    }
}
