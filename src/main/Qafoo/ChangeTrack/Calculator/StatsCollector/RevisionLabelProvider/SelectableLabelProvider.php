<?php

namespace Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;

use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

interface SelectableLabelProvider extends RevisionLabelProvider
{
    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return bool
     */
    public function canProvideLabel(RevisionChanges $revisionChanges);
}
