<?php

namespace Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;

use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider\SelectableLabelProvider;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class DefaultLabelProvider implements SelectableLabelProvider
{
    /**
     * @var string
     */
    private $label;

    /**
     * @param string $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return string
     */
    public function provideLabel(RevisionChanges $revisionChanges)
    {
        return $this->label;
    }

    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return bool
     */
    public function canProvideLabel(RevisionChanges $revisionChanges)
    {
        return true;
    }
}
