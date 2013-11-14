<?php

namespace Qafoo\ChangeTrack\Analyzer\Reflection;

class FileQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testCacheInFind()
    {
        $reflectionQueryMock = $this->getMockBuilder('pdepend\\reflection\\queries\\ReflectionFileQuery')
            ->disableOriginalConstructor()
            ->getMock();

        $fileQuery = new FileQuery($reflectionQueryMock);

        $reflectionQueryMock->expects($this->exactly(2))
            ->method('find')
            ->will($this->returnValue('some value'));

        $fileQuery->find('/foo/bar', 'A');
        $fileQuery->find('/foo/bar', 'A');
        $fileQuery->find('/foo/bar', 'B');
    }
}
