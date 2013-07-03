<?php

namespace Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;

use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class ChainSelectionLabelProvider implements RevisionLabelProvider
{
    /**
     * @var \Qafoo\ChangeTrack\Calculator\RevisionLabelProvider\SelectableLabelProvider[]
     */
    private $chainedLabelProviders = array();

    /**
     * @param \Qafoo\ChangeTrack\Calculator\RevisionLabelProvider\SelectableLabelProvider[] $chainedLabelProviders
     */
    public function __construct(array $chainedLabelProviders)
    {
        foreach ($chainedLabelProviders as $labelProvider) {
            $this->addLabelProvider($labelProvider);
        }
    }

    /**
     * @param \Qafoo\ChangeTrack\Calculator\RevisionLabelProvider\SelectableLabelProvider $labelProvider
     */
    public function addLabelProvider(SelectableLabelProvider $labelProvider)
    {
        $this->chainedLabelProviders[] = $labelProvider;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges $revisionChanges
     */
    public function provideLabel(RevisionChanges $revisionChanges)
    {
        foreach ($this->chainedLabelProviders as $labelProvider) {
            if ($labelProvider->canProvideLabel($revisionChanges)) {
                return $labelProvider->provideLabel($revisionChanges);
            }
        }
        throw new \RuntimeException(
            sprintf(
                'Could not provide label for revision %s',
                $revisionChanges->revision
            )
        );
    }
}
