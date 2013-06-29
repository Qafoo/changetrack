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
     * @param array(PackageChanges) $revisionChanges
     */
    public function __construct($revision, $commitMessage, array $revisionChanges = array())
    {
        parent::__construct($revisionChanges);

        $this->revision = $revision;
        $this->commitMessage = $commitMessage;
    }

    public function createPackageChanges($packageName)
    {
        if (!isset($this[$packageName])) {
            $this[$packageName] = new PackageChanges($packageName);
        }
        return $this[$packageName];
    }
}
