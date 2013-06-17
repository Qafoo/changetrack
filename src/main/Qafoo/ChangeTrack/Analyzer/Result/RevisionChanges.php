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
     * @param array(ClassChanges) $revisionChanges
     */
    public function __construct($revision, $commitMessage, array $revisionChanges = array())
    {
        parent::__construct($revisionChanges);

        $this->revision = $revision;
        $this->commitMessage = $commitMessage;
    }

    public function createClassChanges($className)
    {
        if (!isset($this[$className])) {
            $this[$className] = new ClassChanges($className);
        }
        return $this[$className];
    }
}
