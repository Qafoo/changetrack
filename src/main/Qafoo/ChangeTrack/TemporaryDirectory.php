<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\Filesystem\Filesystem;

class TemporaryDirectory
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $createdDirectories = array();

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(
                sprintf('Temporary directory "%s" does not exist.', $path)
            );
        }
        if (!is_writable($path)) {
            throw new \InvalidArgumentException(
                sprintf('Temporary directory "%s" is not writable.', $path)
            );
        }
        $this->path = $path;
    }

    /**
     * @param string $dirName
     * @return string Created directory path
     */
    public function createDirectory($dirName)
    {
        $dirPath = $this->path . '/' . $dirName;

        if (is_dir($dirPath)) {
            throw new \RuntimeException("Could not create '$dirPath', already exists.");
        }

        mkdir($dirPath);

        $this->createdDirectories[] = $dirPath;

        return $dirPath;
    }

    /**
     * Cleans up all created directories.
     */
    public function cleanup()
    {
        $fsTools = new Filesystem();
        $fsTools->remove($this->createdDirectories);
    }
}
