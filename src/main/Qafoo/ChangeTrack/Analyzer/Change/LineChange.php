<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Arbit\VCSWrapper\Diff;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;
use Qafoo\ChangeTrack\Analyzer\ReflectionLookupFactory;

abstract class LineChange
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\RelfectionLookup
     */
    protected $reflectionLookup;

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

        $lookupFactory = new ReflectionLookupFactory();
        $this->reflectionLookup = $lookupFactory->createReflectionLookup();
    }

    /**
     * Returns a ReflectionMethod, if a method is affected by the change
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout $checkout
     * @param string $revision
     * @param \Qafoo\ChangeTrack\Analyzer\Change\FileChange
     */
    abstract public function determineAffectedArtifact(GitCheckout $checkout, $revision, FileChange $fileChange);

    /**
     * @return int
     */
    public function getAffectedLine()
    {
        return $this->affectedLine;
    }
}
