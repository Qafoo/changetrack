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

    private function createLookup()
    {
        $factory = new ReflectionLookupFactory();
        return $factory->createReflectionLookup();
    }
}
