<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\Filesystem\Filesystem;

class RepositoryFactory
{
    /**
     * @var string
     */
    private $tempRepositoryDir;

    /**
     * @return string
     */
    public function getRepositoryPath()
    {
        $this->prepareIfNecessary();
        return $this->repositoryTempDir;
    }

    /**
     * @return string
     */
    public function getRepositoryUrl()
    {
        $this->prepareIfNecessary();
        return 'file://' . $this->getRepositoryPath();
    }

    public function cleanup()
    {
        if ($this->repositoryTempDir === null) {
            return;
        }
        $this->removeRecursively($this->repositoryTempDir);
    }

    private function prepareIfNecessary()
    {
        if ($this->tempRepositoryDir === null) {
            $this->prepareRepository();
        }
    }

    private function prepareRepository()
    {
        if (version_compare(PHP_VERSION, '5.2.1') < 0) {
            throw new \RuntimeException(
                'PHP version > 5.2.1 required for ZIP extension.'
            );
        }

        $uniqueTempDir = sys_get_temp_dir() . '/' . uniqid('qafoo_changetrack_repository_');

        if (!mkdir($uniqueTempDir)) {
            throw new \RuntimeException(
                "Could not create unique temp dir '$uniqueTempDir' for repository."
            );
        }

        $zipFile = __DIR__ . '/../../_fixtures/daemon_repository.git.zip';

        $zip = new \ZIPArchive();
        $zip->open($zipFile);
        $zip->extractTo($uniqueTempDir);
        $zip->close();

        $this->repositoryTempDir = $uniqueTempDir . '/Daemon';
    }

    protected function removeRecursively($dir)
    {
        $fsTools = new Filesystem();
        if (is_dir($dir)) {
            $fsTools->remove($dir);
        }
    }
}
