<?php

namespace Qafoo\ChangeTrack\Calculator;

use Qafoo\ChangeTrack\Calculator\StatsCollector\PackageStatsCollector;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class StatsCollector
{
    /**
     * @var string
     */
    private $repositoryUrl;

    /**
     * @var \Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider
     */
    private $labelProvider;

    /**
     * @var array
     */
    private $packageStatsCollectors = array();

    /**
     * @param string $repositoryUrl
     * @param \Qafoo\ChangeTrack\Calculator\RevisionLabelProvider $labelProvider
     */
    public function __construct($repositoryUrl, RevisionLabelProvider $labelProvider)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->labelProvider = $labelProvider;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges $revisionChanges
     */
    public function recordRevision(RevisionChanges $revisionChanges)
    {
        $revisionLabel = $this->labelProvider->provideLabel($revisionChanges);
        $this->recordChangesForRevision($revisionChanges, $revisionLabel);
    }

    /**
     * @return array
     */
    public function getStats()
    {
        $stats = new Stats(
            $this->repositoryUrl,
            array_map(
                function ($packageStatsCollector) {
                    return $packageStatsCollector->buildPackageStats();
                },
                $this->packageStatsCollectors
            )
        );
        return $stats;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges $revisionChanges
     * @param string $label
     */
    private function recordChangesForRevision(RevisionChanges $revisionChanges, $label)
    {
        foreach ($revisionChanges->packageChanges as $packageChange) {
            $packageName = $packageChange->packageName;

            if (!isset($this->packageStatsCollectors[$packageName])) {
                $this->packageStatsCollectors[$packageName] = new PackageStatsCollector($packageName);
            }

            foreach ($packageChange->classChanges as $classChange) {
                foreach ($classChange->methodChanges as $methodChange) {
                    $this->packageStatsCollectors[$packageName]
                        ->classStatsCollector($classChange->className)
                        ->methodStatsCollector($methodChange->methodName)
                        ->count($label);
                }
            }
        }
    }

    private function recordChangesForMethod($packageName, $className, $methodName, $label)
    {
        if (!isset($this->stats[$packageName][$className][$methodName][$label])) {
            $this->stats[$packageName][$className][$methodName][$label] = 0;
        }

        $this->stats[$packageName][$className][$methodName][$label]++;
    }
}
