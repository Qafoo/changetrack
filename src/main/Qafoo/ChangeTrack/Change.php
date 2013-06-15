<?php

namespace Qafoo\ChangeTrack;

class Change
{
    const ADDED = 1;

    const REMOVED = -1;

    public $localFile;

    public $affectedLine;

    public $changeType;

    public $revision;

    public $message;

    public function __construct($localFile, $affectedLine, $changeType, $revision, $message)
    {
        $this->localFile = $localFile;
        $this->affectedLine = $affectedLine;
        $this->changeType = $changeType;
        $this->revision = $revision;
        $this->message = $message;
    }
}
