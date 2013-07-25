<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\ResultBuilder\RevisionChangesBuilder;
use Qafoo\ChangeTrack\Analyzer\Result;

class ResultBuilder
{
    /**
     * @var string
     */
    private $repositoryUrl;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ResultBuilder\RevisionChangesBuilder[]
     */
    private $revisionChangesBuilders = array();

    public function __construct($repositoryUrl)
    {
        $this->repositoryUrl = $repositoryUrl;
    }

    /**
     * @param string $revision
     * @return \Qafoo\ChangeTrack\Analyzer\ResultBuilder\RevisionChangesBuilder
     */
    public function revisionChanges($revision)
    {
        if (!isset($this->revisionChangesBuilders[$revision])) {
            $this->revisionChangesBuilders[$revision] = new RevisionChangesBuilder($revision);
        }
        return $this->revisionChangesBuilders[$revision];
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result
     */
    public function buildResult()
    {
        return new Result(
            $this->repositoryUrl,
            array_map(
                function ($revisionChangesBuilder) {
                    return $revisionChangesBuilder->buildRevisionChanges();
                },
                $this->revisionChangesBuilders
            )
        );
    }
}
