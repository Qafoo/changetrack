<?php

namespace Qafoo\ChangeTrack\Analyzer;

class RevisionBoundaries
{
    /**
     * First revision to analyze.
     *
     * @var string
     */
    private $startRevision;

    /**
     * Last revision to analyze.
     *
     * @var string
     */
    private $endRevision;

    /**
     * @param string $startRevision
     * @param string $endRevision
     */
    public function __construct($startRevision, $endRevision)
    {
        $this->startRevision = $startRevision;
        $this->endRevision = $endRevision;
    }

    /**
     * Returns if $startRevision is null or equals $compareRevision
     *
     * @param string $compareRevision
     */
    public function startNotNullAndEquals($compareRevision)
    {
        return ($this->startRevision !== null && $this->startRevision === $compareRevision);
    }

    /**
     * Returns if $endRevision is null or equals $compareRevision
     *
     * @param string $compareRevision
     */
    public function endNotNullAndEquals($compareRevision)
    {
        return ($this->endRevision !== null && $this->endRevision === $compareRevision);
    }

    /**
     * @return string
     */
    public function getStartRevision()
    {
        return $this->startRevision;
    }

    /**
     * @return string
     */
    public function getEndRevision()
    {
        return $this->endRevision;
    }
}
