<?php

namespace Qafoo\ChangeTrack\WorkingDirectory;

class WorkingDirectoryFactory
{
    /**
     * Configured path or null.
     *
     * @var string|null
     */
    private $pathOrNull;

    /**
     * @param string|null $pathOrNull
     */
    public function __construct($pathOrNull)
    {
        $this->pathOrNull = $pathOrNull;
    }

    /**
     * @return \Qafoo\ChangeTrack\WorkingDirectory
     */
    public function createWorkingDirectory()
    {
        if ($this->pathOrNull === null) {
            return new TemporaryDirectory();
        }
        return new ConfigurableDirectory($this->pathOrNull);
    }
}
