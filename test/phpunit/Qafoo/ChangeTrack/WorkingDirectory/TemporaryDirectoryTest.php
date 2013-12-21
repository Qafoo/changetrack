<?php

namespace Qafoo\ChangeTrack\WorkingDirectory;

use org\bovigo\vfs\vfsStream;

class TemporaryDirectoryTest extends \PHPUnit_Framework_TestCase
{
    const VFS = 'temp';

    private $vfsRoot;

    public function setUp()
    {
        $this->vfsRoot = vfsStream::setup(self::VFS);
    }

    public function testCreateDirectoryCreatesDirInSystemTemp()
    {
        $tempDir = new TemporaryDirectory($this->getLocatorMock());

        $tempDir->createDirectory('foo');

        $this->assertEquals(
            1,
            count($this->vfsRoot->getChildren())
        );
    }

    public function testCleanupRemovesDirInSystemTemp()
    {
        $tempDir = new TemporaryDirectory($this->getLocatorMock());

        $tempDir->createDirectory('foo');
        $tempDir->cleanup();

        $this->assertEquals(
            0,
            count($this->vfsRoot->getChildren())
        );
    }

    /**
     * @return \Qafoo\ChangeTrack\WorkingDirectory\SysTempDirLocator
     */
    private function getLocatorMock()
    {
        $locatorMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\WorkingDirectory\\SysTempDirLocator')
            ->disableOriginalConstructor()
            ->getMock();

        $locatorMock->expects($this->any())
            ->method('getTempDir')
            ->will($this->returnValue(vfsStream::url(self::VFS)));

        return $locatorMock;
    }
}
