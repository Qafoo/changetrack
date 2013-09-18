<?php

namespace Qafoo\ChangeTrack\Calculator;

use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;

class StatsCollectorFactory
{
    /**
     * @var \Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider
     */
    private $labelProvider;

    /**
     * @param \Qafoo\ChangeTrack\Calculator\RevisionLabelProvider $labelProvider
     */
    public function __construct(RevisionLabelProvider $labelProvider)
    {
        $this->labelProvider = $labelProvider;
    }

    /**
     * @param string $repositoryUrl
     * @return \Qafoo\ChangeTrack\Calculator\StatsCollector
     */
    public function createStatsCollector($repositoryUrl)
    {
        return new StatsCollector($repositoryUrl, $this->labelProvider);
    }
}
