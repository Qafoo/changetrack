<?php

namespace Qafoo\ChangeTrack\Analyzer;

class ReflectionLookupTest extends \PHPUnit_Framework_TestCase
{
    private $testFile;

    public function setUp()
    {
        $this->testFile = __DIR__ . '/../../../_fixtures/Analyzer.php';
    }

    public function testGetAffectedMethod()
    {
        $lookup = $this->createLookup();

        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 70, 'rev-abc');

        $this->assertEquals(
            'analyze',
            $reflectionMethod->getName()
        );
    }

    public function testGetAffectedMethodFirstLine()
    {
        $lookup = $this->createLookup();

        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 58, 'rev-abc');

        $this->assertEquals(
            'analyze',
            $reflectionMethod->getName()
        );
    }

    public function testGetAffectedMethodLastLine()
    {
        $lookup = $this->createLookup();

        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 82, 'rev-abc');

        $this->assertEquals(
            'analyze',
            $reflectionMethod->getName()
        );
    }

    public function testGetAffectedMethodNullOutsideMethod()
    {
        $lookup = $this->createLookup();

        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 57, 'rev-abc');

        $this->assertNull($reflectionMethod);
    }

    public function testGetAffectedMethodCaches()
    {
        $fileQueryMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\Analyzer\\Reflection\\FileQuery')
            ->disableOriginalConstructor()
            ->getMock();

        $fileQueryMock->expects($this->once())
            ->method('find')
            ->will($this->returnValue(array()));

        $lookup = new ReflectionLookup($fileQueryMock);

        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 57, 'rev-abc');
        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 57, 'rev-abc');
    }

    public function testGetAffectedMethodClearsCache()
    {
        $fileQueryMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\Analyzer\\Reflection\\FileQuery')
            ->disableOriginalConstructor()
            ->getMock();

        $fileQueryMock->expects($this->exactly(2))
            ->method('find')
            ->will($this->returnValue(array()));

        $lookup = new ReflectionLookup($fileQueryMock);

        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 57, 'rev-abc');
        $reflectionMethod = $lookup->getAffectedMethod($this->testFile, 57, 'rev-def');
    }

    private function createLookup()
    {
        $factory = new ReflectionLookupFactory();
        return $factory->createReflectionLookup();
    }
}
