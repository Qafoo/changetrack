<?php

namespace Qafoo\ChangeTrack\Calculator\Stats;

class MethodStats
{
    /**
     * @var string
     */
    public $methodName;

    /**
     * @var array(string => int)
     */
    public $labelStats;

    /**
     * @param string $methodName
     * @param array(string => int) $labelStats
     */
    public function __construct($methodName, array $labelStats)
    {
        $this->methodName = $methodName;
        $this->labelStats = $labelStats;
    }
}
