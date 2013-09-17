<?php

namespace Qafoo\ChangeTrack\Calculator;

use Qafoo\ChangeTrack\Calculator\Stats\PackageStats;

class Stats
{
    /**
     * @var string
     */
    public $repositoryUrl;

    /**
     * @var \Qafoo\ChangeTrack\Calculator\Stats\PackageStats[]
     */
    public $packageStats;

    /**
     * @param string $repositoryUrl
     * @param \Qafoo\ChangeTrack\Calculator\Stats\PackageStats[] $packageStats
     */
    public function __construct($repositoryUrl, array $packageStats)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->packageStats = $packageStats;
    }
}
