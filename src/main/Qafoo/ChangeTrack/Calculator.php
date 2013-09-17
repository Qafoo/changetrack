<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\Analyzer\Result;

use Qafoo\ChangeTrack\Calculator\StatsCollector;
use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider\ChainSelectionLabelProvider;
use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider\RegexLabelProvider;
use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider\DefaultLabelProvider;

class Calculator
{
    private $analysisResult;

    public function __construct(Result $analysisResult)
    {
        $this->analysisResult = $analysisResult;
    }

    public function calculateStats()
    {
        $statsCollector = new StatsCollector(
            $this->analysisResult->repositoryUrl,
            new ChainSelectionLabelProvider(
                array(
                    new RegexLabelProvider('(fixed)i', 'fix'),
                    new RegexLabelProvider('(implemented)i', 'implement'),
                    new DefaultLabelProvider('misc')
                )
            )
        );

        foreach ($this->analysisResult->revisionChanges as $revisionChange) {
            $statsCollector->recordRevision($revisionChange);
        }

        return $statsCollector->getStats();
    }
}
