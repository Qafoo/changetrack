<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Qafoo\ChangeTrack\Analyzer\Checkout;
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
     * @param \Qafoo\ChangeTrack\Analyzer\Checkout $checkout
     * @param \Qafoo\ChangeTrack\Analyzer\ReflectionLookup $reflectionLookup
     * @param string $revision
     * @param \Qafoo\ChangeTrack\Analyzer\Change\FileChange
     */
    abstract public function determineAffectedArtifact(
        Checkout $checkout,
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
