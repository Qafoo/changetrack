<?php

namespace Qafoo\ChangeTrack;

class TemporaryDirectoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @ticket #13
     */
    public function testExceptionOnNonExistentDirectory()
    {
        $this->setExpectedException('InvalidArgumentException');

        $tempDir = new TemporaryDirectory('hopefully/does/not/exist');
    }
}
