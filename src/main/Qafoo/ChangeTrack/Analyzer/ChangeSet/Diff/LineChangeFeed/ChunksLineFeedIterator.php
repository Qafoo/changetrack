<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

class ChunksLineFeedIterator extends LineChangeFeed
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
