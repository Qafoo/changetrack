<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\WorkingDirectory\ConfigurableDirectory;
use Symfony\Component\Filesystem\Filesystem;

class CheckoutAwareTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Qafoo\ChangeTrack\WorkingDirectory\ConfigurableDirectory
     */
    private $tempDir;

    /**
     * @var string
     */
    private $checkoutPath;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var \Qafoo\ChangeTrack\RepositoryFactory
     */
    private static $repositoryFactory;

    public static function setUpBeforeClass()
    {
        self::$repositoryFactory = new RepositoryFactory();
    }

    public static function tearDownAfterClass()
    {
        self::$repositoryFactory->cleanup();
    }

    public function setup()
    {
        $this->tempDir = new ConfigurableDirectory(__DIR__ . '/../../../../src/var/tmp');

        $this->cachePath = $this->tempDir->createDirectory('cache');
        $this->checkoutPath =  $this->tempDir->createDirectory('checkout');
    }

    public function tearDown()
    {
        $this->tempDir->cleanup();
    }

    /**
     * @return string
     */
    protected function getRepositoryUrl()
    {
        return self::$repositoryFactory->getRepositoryUrl();
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
