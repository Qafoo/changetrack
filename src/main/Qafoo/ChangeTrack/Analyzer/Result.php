<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class Result extends \ArrayObject
{
    /**
     * @var string
     */
    public $repositoryUrl;

    /**
     * @param string $repositoryUrl
     * @param array(RevisionChanges) $revisionChanges
     */
    public function __construct($repositoryUrl, array $revisionChanges = array())
    {
        parent::__construct($revisionChanges);

        $this->repositoryUrl = $repositoryUrl;
    }
}
