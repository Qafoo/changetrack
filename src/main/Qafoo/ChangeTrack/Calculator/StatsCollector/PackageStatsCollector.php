<?php

namespace Qafoo\ChangeTrack\Calculator\StatsCollector;

use Qafoo\ChangeTrack\Calculator\Stats\PackageStats;

class PackageStatsCollector
{
    /**
     * @var \Qafoo\ChangeTrack\Calculator\StatsCollector\ClassStatsCollector[]
     */
    private $classStatsCollectors = array();

    /**
     * @var string
     */
    private $packageName;

    /**
     * @param string $packageName
     */
    public function __construct($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * @param string $className
     * @return \Qafoo\ChangeTrack\Calculator\StatsCollector\ClassStatsCollector
     */
    public function classStatsCollector($className)
    {
        if (!isset($this->classStatsCollectors[$className])) {
            $this->classStatsCollectors[$className] = new ClassStatsCollector($className);
        }
        return $this->classStatsCollectors[$className];
    }

    /**
     * @return \Qafoo\ChangeTrack\Calculator\Stats\PackageStats
     */
    public function buildPackageStats()
    {
        return new PackageStats(
            $this->packageName,
            array_map(
                function ($classStatsCollector) {
                    return $classStatsCollector->buildClassStats();
                },
                $this->classStatsCollectors
            )
        );
    }
}
