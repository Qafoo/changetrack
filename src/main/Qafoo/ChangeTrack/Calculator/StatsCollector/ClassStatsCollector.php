<?php

namespace Qafoo\ChangeTrack\Calculator\StatsCollector;

use Qafoo\ChangeTrack\Calculator\Stats\ClassStats;

class ClassStatsCollector
{
    /**
     * @var \Qafoo\ChangeTrack\Calculator\StatsCollector\MethodStatsCollector[]
     */
    private $methodStatsCollectors = array();

    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param string $methodName
     * @return \Qafoo\ChangeTrack\Calculator\StatsCollector\MethodStatsCollector
     */
    public function methodStatsCollector($methodName)
    {
        if (!isset($this->methodStatsCollectors[$methodName])) {
            $this->methodStatsCollectors[$methodName] = new MethodStatsCollector($methodName);
        }
        return $this->methodStatsCollectors[$methodName];
    }

    /**
     * @return \Qafoo\ChangeTrack\Calculator\Stats\ClassStats
     */
    public function buildClassStats()
    {
        return new ClassStats(
            $this->className,
            array_map(
                function ($methodStatsCollector) {
                    return $methodStatsCollector->buildMethodStats();
                },
                $this->methodStatsCollectors
            )
        );
    }
}
