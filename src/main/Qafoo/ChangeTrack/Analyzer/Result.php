<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class Result
{
    /**
     * @var string
     */
    public $repositoryUrl;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges[]
     */
    public $revisionChanges;

    /**
     * @param string $repositoryUrl
     * @param array(RevisionChanges) $revisionChanges
     */
    public function __construct($repositoryUrl, array $revisionChanges = array())
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->revisionChanges = $revisionChanges;
    }
}
