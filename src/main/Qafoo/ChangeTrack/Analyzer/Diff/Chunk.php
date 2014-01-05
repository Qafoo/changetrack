<?php

namespace Qafoo\ChangeTrack\Analyzer\Diff;

class Chunk
{
    /**
     * @var int
     */
    public $start = null;

    /**
     * @var int
     */
    public $startRange = 1;

    /**
     * @var int
     */
    public $end = null;

    /**
     * @var int
     */
    public $endRange = 1;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Diff\Line[]
     */
    public $lines = null;

    /**
     * @param int $start
     * @param int $startRange
     * @param int $end
     * @param int $endRange
     * @param \Qafoo\ChangeTrack\Analyzer\Diff\Line[] $lines
     */
    public function __construct($start, $startRange, $end, $endRange, array $lines)
    {
        $this->start = $start;
        $this->startRange = $startRange;
        $this->end = $end;
        $this->endRange = $endRange;
        $this->lines = $lines;
    }
}
