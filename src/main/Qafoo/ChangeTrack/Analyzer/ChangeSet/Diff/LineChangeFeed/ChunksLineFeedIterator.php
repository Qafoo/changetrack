<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;
use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed;

class ChunksLineFeedIterator extends LineChangeFeed
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ReflectionLookup
     */
    private $reflectionLookup;

    private $chunks;

    public function __construct(ReflectionLookup $reflectionLookup, array $chunks)
    {
        $this->reflectionLookup = $reflectionLookup;
        $this->chunks = $chunks;
    }

    public function getIterator()
    {
        $appendIterator = new \AppendIterator();
        $reflectionLookup = $this->reflectionLookup;

        array_walk(
            $this->chunks,
            function ($chunk) use ($reflectionLookup, $appendIterator) {
                $appendIterator->append(
                    new \IteratorIterator(
                        new ChunkLineFeedGenerator($reflectionLookup, $chunk)
                    )
                );
            }
        );

        return $appendIterator;
    }
}
