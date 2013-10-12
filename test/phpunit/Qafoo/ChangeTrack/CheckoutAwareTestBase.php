<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\Filesystem\Filesystem;

class CheckoutAwareTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $repositoryUrl = 'https://github.com/tobyS/Daemon.git';

    /**
     * @var string
     */
    private $checkoutPath;

    /**
     * @var string
     */
    private $cachePath;

    public function setup()
    {
        if (isset($_ENV['repositoryUrl'])) {
            $this->repositoryUrl = $_ENV['repositoryUrl'];
        }

        $this->cachePath = __DIR__ . '/../../../../src/var/tmp/cache';
        $this->checkoutPath =  __DIR__ . '/../../../../src/var/tmp/checkout';

        $this->cleanupTempDir($this->getCachePath());
        $this->cleanupTempDir($this->getCheckoutPath());
    }

    /**
     * @return string
     */
    protected function getRepositoryUrl()
    {
        return $this->repositoryUrl;
    }

    /**
     * @return string
     */
    protected function getCachePath()
    {
        return $this->cachePath;
    }

    /**
     * @return string
     */
    protected function getCheckoutPath()
    {
        return $this->checkoutPath;
    }

    /**
     * @param string $path
     */
    protected function cleanupTempDir($path)
    {
        $fsTools = new Filesystem();
        if (is_dir($path)) {
            $fsTools->remove($path);
        }
        $fsTools->mkdir($path);
    }
}
