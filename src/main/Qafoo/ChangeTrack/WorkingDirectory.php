<?php

namespace Qafoo\ChangeTrack;

abstract class WorkingDirectory
{
    /**
     * @param string $dirName
     * @return string Created directory path
     */
    abstract public function createDirectory($dirName);

    /**
     * Cleans up all created directories.
     */
    abstract public function cleanup();
}
