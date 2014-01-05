<?php

namespace Qafoo\ChangeTrack\Analyzer\Diff;

class Diff
{
    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $to;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Diff\Chunk[]
     */
    public $chunks;

    /**
     * @param string $from
     * @param string $to
     * @param \Qafoo\ChangeTrack\Analyzer\Diff\Chunk[] $chunks
     */
    public function __construct($from, $to, array $chunks)
    {
        $this->from = $from;
        $this->to = $to;
        $this->chunks = $chunks;
    }
}
