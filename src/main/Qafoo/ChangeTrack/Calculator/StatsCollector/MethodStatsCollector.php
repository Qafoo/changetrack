<?php

namespace Qafoo\ChangeTrack\Calculator\StatsCollector;

use Qafoo\ChangeTrack\Calculator\Stats\MethodStats;

class MethodStatsCollector
{
    /**
     * @var array(string => int)
     */
    private $labelStats = array();

    /**
     * @var string
     */
    private $methodName;

    /**
     * @param string $methodName
     */
    public function __construct($methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * @param string $label
     */
    public function count($label)
    {
        if (!isset($this->labelStats[$label])) {
            $this->labelStats[$label] = 0;
        }
        $this->labelStats[$label]++;
        return $this;
    }

    /**
     * @return \Qafoo\ChangeTrack\Calculator\Stats\MethodStats
     */
    public function buildMethodStats()
    {
        return new MethodStats(
            $this->methodName,
            $this->labelStats
        );
    }
}
