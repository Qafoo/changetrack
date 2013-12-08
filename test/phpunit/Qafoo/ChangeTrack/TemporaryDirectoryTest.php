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
}
