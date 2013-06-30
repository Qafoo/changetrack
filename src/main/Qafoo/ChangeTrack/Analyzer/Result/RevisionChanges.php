<?php

namespace Qafoo\ChangeTrack\Analyzer\Result;

class RevisionChanges extends \ArrayObject
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
     * @param string $revision
     * @param string $commitMessage
     * @param \Qafoo\ChangeTrack\Analyzer\Result\PackageChanges[] $packageChanges
     */
    public function __construct($revision, $commitMessage, array $packageChanges)
    {
        parent::__construct($packageChanges);

        $this->revision = $revision;
        $this->commitMessage = $commitMessage;
    }
}
