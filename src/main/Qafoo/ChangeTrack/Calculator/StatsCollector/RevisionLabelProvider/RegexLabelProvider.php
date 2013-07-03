<?php

namespace Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider;

use Qafoo\ChangeTrack\Calculator\StatsCollector\RevisionLabelProvider\SelectableLabelProvider;
use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class RegexLabelProvider implements SelectableLabelProvider
{
    /**
     * @var string
     */
    private $commitMessageRegex;

    /**
     * @var string
     */
    private $label;

    /**
     * @param string $commitMessageRegex
     * @param string $label
     */
    public function __construct($commitMessageRegex, $label)
    {
        $this->commitMessageRegex = $commitMessageRegex;
        $this->label = $label;
    }

    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return string
     */
    public function provideLabel(RevisionChanges $revisionChanges)
    {
        if (!$this->canProvideLabel($revisionChanges)) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to provide label for revision %s',
                    $revisionChanges->revision
                )
            );
        }
        return $this->label;
    }

    /**
     * @param \Qafoo\Analyzer\Result\RevisionChanges $revisionChanges
     * @return bool
     */
    public function canProvideLabel(RevisionChanges $revisionChanges)
    {
        return $this->hasMatches($this->matchRegex($revisionChanges));
    }

    /**
     * @param array $matches
     * @return bool
     */
    private function hasMatches(array $matches)
    {
        return (count($matches) > 0);
    }

    /**
     * @param RevisionChanges $revisionChanges
     * @return array
     */
    private function matchRegex(RevisionChanges $revisionChanges)
    {
        $matches = null;
        preg_match($this->commitMessageRegex, $revisionChanges->commitMessage, $matches);
        return $matches == null ? array() : $matches;
    }
}
