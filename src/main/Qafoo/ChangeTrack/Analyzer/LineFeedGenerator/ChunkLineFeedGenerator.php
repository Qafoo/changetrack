<?php

namespace Qafoo\ChangeTrack\Analyzer\LineFeedGenerator;

use Qafoo\ChangeTrack\Analyzer\LineFeedGenerator;

use Arbit\VCSWrapper\Diff;

class ChunkLineFeedGenerator extends LineFeedGenerator
{
    private $chunk;

    public function __construct(Diff\Chunk $chunk)
    {
        $this->chunk = $chunk;
    }

    public function feedLines()
    {
        $lineCount = count($this->chunk->lines);
        $lineNumber = $this->chunk->end;

        for ($lineOffset = 0; $lineOffset < $lineCount; $lineOffset++) {
            $line = $this->chunk->lines[$lineOffset];

            switch ($line->type) {
                case Diff\Line::ADDED:
                    yield $lineNumber;
                    $lineNumber++;
                    break;
                case Diff\Line::UNCHANGED:
                    $lineNumber++;
                    break;
                case Diff\Line::REMOVED:
                    yield $lineNumber;
                    break;
            }
        }

    }
}
