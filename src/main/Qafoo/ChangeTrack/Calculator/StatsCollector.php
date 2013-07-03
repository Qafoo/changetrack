<?php

namespace Qafoo\ChangeTrack\Calculator;

use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class StatsCollector
{
    /**
     * @var \Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider
     */
    private $labelProvider;

    /**
     * @var array
     */
    private $stats = array();

    /**
     * @param \Qafoo\ChangeTrack\Calculator\RevisionLabelProvider $labelProvider
     */
    public function __construct(RevisionLabelProvider $labelProvider)
    {
        $this->labelProvider = $labelProvider;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges $revisionChanges
     */
    public function recordRevision(RevisionChanges $revisionChanges)
    {
        $revisionLabel = $this->labelProvider->provideLabel($revisionChanges);
        $this->recordChangesFromRevision($revisionChanges, $revisionLabel);
    }

    /**
     * @return array
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges $revisionChanges
     * @param string $label
     */
    private function recordChangesFromRevision(RevisionChanges $revisionChanges, $label)
    {
        foreach ($revisionChanges as $packageChanges) {
            $packageName = $packageChanges->packageName;

            if (!isset($this->stats[$packageName])) {
                $this->stats[$packageName] = array();
            }

            foreach ($packageChanges as $classChanges) {
                $className = $classChanges->className;
                if (!isset($this->stats[$packageName][$className])) {
                    $this->stats[$packageName][$className] = array();
                }

                foreach ($classChanges as $methodChanges) {
                    $methodName = $methodChanges->methodName;

                    if (!isset($this->stats[$packageName][$className][$methodName])) {
                        $this->stats[$packageName][$className][$methodName] = array();
                    }

                    if (!isset($this->stats[$packageName][$className][$methodName][$label])) {
                        $this->stats[$packageName][$className][$methodName][$label] = 0;
                    }

                    $this->stats[$packageName][$className][$methodName][$label]++;
                }
            }
        }
    }
}
