<?php

namespace Qafoo\ChangeTrack\Calculator\Stats;

class ClassStats
{
    /**
     * @var string
     */
    public $className;

    /**
     * @var \Qafoo\ChangeTrack\Calculator\Stats\MethodStats[]
     */
    public $methodStats;

    /**
     * @param string $className
     * @param \Qafoo\ChangeTrack\Calculator\Stats\MethodStats[] $methodStats
     */
    public function __construct($className, $methodStats)
    {
        $this->className = $className;
        $this->methodStats = $methodStats;
    }
}
