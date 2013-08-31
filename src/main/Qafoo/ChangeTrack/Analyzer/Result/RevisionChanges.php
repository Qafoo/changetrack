<?php

namespace Qafoo\ChangeTrack\Analyzer\Result;

class RevisionChanges
{
    /**
     * @var string
     */
    public $revision;

    /**
     * @var string
     */
    public $commitMessage;

    /**
     * @var Qafoo\ChangeTrack\Analyzer\Result\PackageChanges[]
     */
    public $packageChanges;

    /**
     * @param string $revision
     * @param string $commitMessage
     * @param \Qafoo\ChangeTrack\Analyzer\Result\PackageChanges[] $packageChanges
     */
    public function __construct($revision, $commitMessage, array $packageChanges)
    {
        $this->revision = $revision;
        $this->commitMessage = $commitMessage;
        $this->packageChanges = $packageChanges;
    }
}
