<?php

namespace Qafoo\ChangeTrack\Analyzer\ResultBuilder;

use Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges;

class RevisionChangesBuilder
{
    /**
     * @var string
     */
    private $revision;

    /**
     * @var string
     */
    private $commitMessage;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ResultBuilder\PackageChangesBuilder[]
     */
    private $packageChangesBuilders = array();

    /**
     * @param string $revision
     */
    public function __construct($revision)
    {
        $this->revision = $revision;
    }

    /**
     * @param string $commitMessage
     */
    public function commitMessage($commitMessage)
    {
        $this->commitMessage = $commitMessage;
        return $this;
    }

    /**
     * @param string $packageName
     * @return \Qafoo\ChangeTrack\Analyzer\ResultBuilder\PackageChangesBuilder
     */
    public function packageChanges($packageName)
    {
        if (!isset($this->packageChangesBuilders[$packageName])) {
            $this->packageChangesBuilders[$packageName] = new PackageChangesBuilder(
                $packageName
            );
        }
        return $this->packageChangesBuilders[$packageName];
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result\RevisionChanges
     */
    public function buildRevisionChanges()
    {
        return new RevisionChanges(
            $this->revision,
            $this->commitMessage,
            array_map(
                function ($packageChangesBuilder) {
                    return $packageChangesBuilder->buildPackageChanges();
                },
                $this->packageChangesBuilders
            )
        );
    }
}
