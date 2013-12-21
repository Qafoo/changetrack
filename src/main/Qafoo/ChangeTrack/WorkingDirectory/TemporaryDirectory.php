<?php

namespace Qafoo\ChangeTrack\WorkingDirectory;

use Qafoo\ChangeTrack\WorkingDirectory;
use Symfony\Component\Filesystem\Filesystem;

class TemporaryDirectory extends WorkingDirectory
{
    /**
     * @var \Qafoo\ChangeTrack\WorkingDirectory\ConfigurableDirectory
     */
    private $innerDirectory;

    /**
     * @var string
     */
    private $directoryPath;

    /**
     * @var \Qafoo\ChangeTrack\WorkingDirectory\SysTempDirLocator
     */
    private $sysTempDirLocator;

    /**
     * @param \Qafoo\ChangeTrack\WorkingDirectory\SysTempDirLocator $sysTempDirLocator
     */
    public function __construct(SysTempDirLocator $sysTempDirLocator = null)
    {
        if ($sysTempDirLocator === null) {
            $sysTempDirLocator = new SysTempDirLocator();
        }
        $this->sysTempDirLocator = $sysTempDirLocator;
    }

    /**
     * @param string $dirName
     * @return string Created directory path
     */
    public function createDirectory($dirName)
    {
        return $this->getTemporaryDirectory()->createDirectory($dirName);
    }

    /**
     * Cleans up all created directories.
     */
    public function cleanup()
    {
        $this->getTemporaryDirectory()->cleanup();
        $this->removeTemporaryDirectory();
    }

    /**
     * Returns a WorkingDirectory, creates a temporary one of needed.
     *
     * @return \Qafoo\ChangeTrack\WorkingDirectory
     */
    private function getTemporaryDirectory()
    {
        if ($this->innerDirectory === null) {
            $this->directoryPath = $this->createSystemTempDir();
            $this->innerDirectory = new ConfigurableDirectory($this->directoryPath);
        }
        return $this->innerDirectory;
    }

    /**
     * Creates a temporary directory in sys_get_temp_dir() and returns the path.
     *
     * @return string
     */
    private function createSystemTempDir()
    {
        $sysTempDir = $this->sysTempDirLocator->getTempDir();

        do {
            $candidatePath = $sysTempDir . '/' . uniqid('changetrack');
        } while (file_exists($candidatePath));

        mkdir($candidatePath);
        return $candidatePath;
    }

    /**
     * Removes the created temporary directory.
     */
    private function removeTemporaryDirectory()
    {
        if ($this->directoryPath === null) {
            return;
        }
        unset($this->innerDirectory);

        $fsTools = new Filesystem();
        $fsTools->remove($this->directoryPath);

        unset($this->directoryPath);
    }
}
