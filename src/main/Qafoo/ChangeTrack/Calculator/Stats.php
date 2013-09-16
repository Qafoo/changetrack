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

    public function __construct($repositoryUrl, array $packageStats)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->packageStats = $packageStats;
    }
}
