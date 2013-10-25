<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

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
     * @param string $beforePath
     * @param string $afterPath
     * @return string
     */
    public function getAffectedFile($beforePath, $afterPath)
    {
        return $this->lineChange->determineAffectedFile($beforePath, $afterPath, $this->fileChange);
    }

    /**
     * @return int
     */
    public function getAffectedLine()
    {
        return $this->lineChange->getAffectedLine();
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Change\LineChange
     */
    public function getLineChange()
    {
        return $this->lineChange;
    }
}
