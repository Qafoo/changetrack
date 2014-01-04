<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;
use Qafoo\ChangeTrack\Analyzer\Checkout;

class LocalChange
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Change\FileChange
     */
    private $fileChange;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Change\LineChange
     */
    private $lineChange;

    public function __construct(FileChange $fileChange, LineChange $lineChange)
    {
        $this->fileChange = $fileChange;
        $this->lineChange = $lineChange;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Checkout $checkout
     */
    public function determineAffectedArtifact(Checkout $checkout, ReflectionLookup $reflectionLookup, $revision)
    {
        return $this->lineChange->determineAffectedArtifact(
            $checkout,
            $reflectionLookup,
            $revision,
            $this->fileChange
        );
    }

    public function getFileChange()
    {
        return $this->fileChange;
    }

    public function getLineChange()
    {
        return $this->lineChange;
    }
}
