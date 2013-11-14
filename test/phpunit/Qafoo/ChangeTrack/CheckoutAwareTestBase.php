<?php

namespace Qafoo\ChangeTrack;

use Symfony\Component\Filesystem\Filesystem;

class CheckoutAwareTestBase extends \PHPUnit_Framework_TestCase
{
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
