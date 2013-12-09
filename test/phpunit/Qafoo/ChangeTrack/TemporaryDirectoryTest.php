<?php

namespace Qafoo\ChangeTrack;

use org\bovigo\vfs\vfsStream;

class TemporaryDirectoryTest extends \PHPUnit_Framework_TestCase
{
    const VFS = 'temp';

    private $vfsRoot;

    public function setUp()
    {
        $this->vfsRoot = vfsStream::setup(self::VFS);
    }

    /**
     * @ticket #13
     */
    public function testExceptionOnNonExistentDirectory()
    {
        $this->setExpectedException('InvalidArgumentException');

        $tempDir = new TemporaryDirectory(vfsStream::url(self::VFS) . '/not/exists');
    }

    public function testCreateDirectory()
    {
        $vfsDir = vfsStream::url(self::VFS) . '/temp';
        mkdir($vfsDir);

        $tempDir = new TemporaryDirectory($vfsDir);

        $createdDir = $tempDir->createDirectory('some');

        $expectedDir = $vfsDir . '/some';

        $this->assertTrue(is_dir($expectedDir));
        $this->assertEquals($expectedDir, $createdDir);
    }

    public function testDirectoriesCleanedUp()
    {
        $vfsDir = vfsStream::url(self::VFS) . '/temp';
        mkdir($vfsDir);

        $tempDir = new TemporaryDirectory($vfsDir);

        $firstDir = $tempDir->createDirectory('some');
        $secondDir = $tempDir->createDirectory('thing');

        mkdir($firstDir . '/foo');
        touch($firstDir . '/foo/bar');

        $tempDir->cleanup();

        $this->assertFalse(is_dir($firstDir));
        $this->assertFalse(is_dir($secondDir));
    }

    public function testCreateDirectoryExceptionOnDuplicate()
    {
        $vfsDir = vfsStream::url(self::VFS) . '/temp';
        mkdir($vfsDir);

        $tempDir = new TemporaryDirectory($vfsDir);

        $createdDir = $tempDir->createDirectory('some');

        $this->setExpectedException('RuntimeException');
        $createdDir = $tempDir->createDirectory('some');
    }

    public function testCreateDirectoryRemovesPrevious()
    {
        $vfsDir = vfsStream::url(self::VFS) . '/temp';
        mkdir($vfsDir);

        $previousDir = $vfsDir . '/some';
        mkdir($previousDir);
        touch($previousDir . '/file');
        mkdir($previousDir . '/dir');

        $tempDir = new TemporaryDirectory($vfsDir);

        $createdDir = $tempDir->createDirectory('some');

        $this->assertEquals($previousDir, $createdDir);
        $this->assertFalse(file_exists($previousDir . '/file'));
        $this->assertFalse(is_dir($previousDir . '/dir'));
    }
}
