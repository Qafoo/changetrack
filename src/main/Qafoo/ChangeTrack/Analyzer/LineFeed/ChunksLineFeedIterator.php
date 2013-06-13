<?php

namespace Qafoo\ChangeTrack\Analyzer\LineFeed;

use Qafoo\ChangeTrack\Analyzer\LineFeed;

class ChunksLineFeedIterator extends LineFeed
{
    private $chunks;

    public function __construct(array $chunks)
    {
        $this->chunks = $chunks;
    }

    public function getIterator()
    {
        $appendIterator = new \AppendIterator();

        array_walk(
            $this->chunks,
            function ($chunk) use ($appendIterator) {
                $appendIterator->append(
                    new \IteratorIterator(
                        new ChunkLineFeedGenerator($chunk)
                    )
                );
            }
        );

        return $appendIterator;
    }
}
