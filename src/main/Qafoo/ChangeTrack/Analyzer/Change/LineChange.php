<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Arbit\VCSWrapper\Diff;

abstract class LineChange
{
    /**
     * @var int
     */
    private $affectedLine;

    /**
     * @param int $affectedLine
     */
    public function __construct($affectedLine)
    {
        $this->affectedLine = $affectedLine;
    }

    /**
     * Returns the absolute path of the affected file.
     *
     * @param string $beforePath
     * @param string $afterPath
     * @param \Qafoo\ChangeTrack\Analyzer\Change\FileChange $fileChange
     */
    abstract public function determineAffectedFile($beforePath, $afterPath, FileChange $fileChange);

    /**
     * @return int
     */
    public function getAffectedLine()
    {
        return $this->affectedLine;
    }
}
