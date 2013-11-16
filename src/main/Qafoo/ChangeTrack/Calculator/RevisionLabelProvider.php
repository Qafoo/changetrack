<?php

namespace Qafoo\ChangeTrack\Calculator;

use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

interface RevisionLabelProvider
{
    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return string
     */
    public function provideLabel(RevisionChanges $revisionChanges);
}
