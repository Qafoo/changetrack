<?php

namespace Qafoo\ChangeTrack\Calculator\Stats;

class PackageStats
{
    /**
     * @var string
     */
    public $packageName;

    /**
     * @var \Qafoo\ChangeTrack\Calculator\Stats\ClassStats[]
     */
    public $classStats;

    /**
     * @param string $packageName
     * @param \Qafoo\ChangeTrack\Calculator\Stats\ClassStats[] $classStats
     */
    public function __construct($packageName, array $classStats)
    {
        $this->packageName = $packageName;
        $this->classStats = $classStats;
    }
}
