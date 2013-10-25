<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

class FileChange
{
    /**
     * @var string
     */
    private $fromFile;

    /**
     * @var string
     */
    private $toFile;

    /**
     * @param string $fromFile
     * @param string $toFile
     */
    public function __construct($fromFile, $toFile)
    {
        $this->fromFile = $this->stripLeadingSlash($fromFile);
        $this->toFile = $this->stripLeadingSlash($toFile);
    }

    /**
     * @return string
     */
    public function getFromFile()
    {
        return $this->fromFile;
    }

    /**
     * @return string
     */
    public function getToFile()
    {
        return $this->toFile;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function stripLeadingSlash($fileName)
    {
        if (substr($fileName, 0, 1) == '/') {
            $fileName = substr($fileName, 1);
        }
        return $fileName;
    }
}
