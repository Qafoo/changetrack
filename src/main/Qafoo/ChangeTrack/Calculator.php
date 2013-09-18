<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\Analyzer\Result;

use Qafoo\ChangeTrack\Calculator\StatsCollectorFactory;

class Calculator
{
    /**
     * @var \Qafoo\ChangeTrack\Calculator\StatsCollectorFactory
     */
    private $statsCollectorFactory;

    /**
     * @param \Qafoo\ChangeTrack\Calculator\StatsCollectorFactory $statsCollectorFactory
     */
    public function __construct(StatsCollectorFactory $statsCollectorFactory)
    {
        $this->statsCollectorFactory = $statsCollectorFactory;
    }

    public function calculateStats(Result $analysisResult)
    {
        $statsCollector = $this->statsCollectorFactory->createStatsCollector(
            $analysisResult->repositoryUrl
        );

        foreach ($analysisResult->revisionChanges as $revisionChange) {
            $statsCollector->recordRevision($revisionChange);
        }

        return $statsCollector->getStats();
    }
}
