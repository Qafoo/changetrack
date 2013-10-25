<?php

namespace Qafoo\ChangeTrack\Analyzer;

class Change
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Change\LocalChange
     */
    private $localChange;

    /**
     * @var string
     */
    private $revision;

    /**
     * @var string
     */
    private $message;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Change\LocalChange $localChange
     * @param string $revision
     * @param string $message
     */
    public function __construct(Change\LocalChange $localChange, $revision, $message)
    {
        $this->localChange = $localChange;
        $this->revision = $revision;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getAffectedLine()
    {
        return $this->localChange->getAffectedLine();
    }

    /**
     * @param string $beforePath
     * @param string $afterPath
     *
     */
    public function getAffectedFile($beforePath, $afterPath)
    {
        return $this->localChange->getAffectedFile($beforePath, $afterPath);
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Change\LineChange
     */
    public function getLineChange()
    {
        return $this->localChange->getLineChange();
    }
}
