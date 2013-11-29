<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Arbit\VCSWrapper\Diff;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;
use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;

abstract class LineChange
{
    /**
     * @var int
     */
    protected $affectedLine;

    /**
     * @param int $affectedLine
     */
    public function __construct($affectedLine)
    {
        $this->affectedLine = $affectedLine;
    }

    /**
     * Returns a ReflectionMethod, if a method is affected by the change
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout $checkout
     * @param \Qafoo\ChangeTrack\Analyzer\ReflectionLookup $reflectionLookup
     * @param string $revision
     * @param \Qafoo\ChangeTrack\Analyzer\Change\FileChange
     */
    abstract public function determineAffectedArtifact(
        GitCheckout $checkout,
        ReflectionLookup $reflectionLookup,
        $revision,
        FileChange $fileChange
    );

    /**
     * @return int
     */
    public function getAffectedLine()
    {
        return $this->affectedLine;
    }
}
