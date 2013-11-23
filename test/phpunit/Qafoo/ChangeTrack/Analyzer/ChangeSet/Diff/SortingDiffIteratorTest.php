<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\Change;

class SortingDiffIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ReflectionLookup
     */
    private $reflectionLookupMock;

    public function setUp()
    {
        $this->reflectionLookupMock = $this->getMockBuilder(
            'Qafoo\\ChangeTrack\\Analyzer\\ReflectionLookup'
        )->disableOriginalConstructor()->getMock();
    }

    public function testSortChangesByType()
    {
        $sortIterator = new SortingDiffIterator(
            $this->getDiffIteratorMock(
                array(
                    ($a = new Change\LocalChange(
                        new Change\FileChange('/foo', '/foo'),
                        $this->createLineRemovedChange(3)
                    )),
                    ($b = new Change\LocalChange(
                        new Change\FileChange('/foo', '/foo'),
                        $this->createLineAddedChange(3)
                    )),
                    ($c = new Change\LocalChange(
                        new Change\FileChange('/foo', '/foo'),
                        $this->createLineRemovedChange(4)
                    )),
                )
            )
        );

        $this->assertIteratorEqualsArray(
            array($a, $c, $b),
            $sortIterator
        );
    }

    public function testSortChangesByFile()
    {
        $sortIterator = new SortingDiffIterator(
            $this->getDiffIteratorMock(
                array(
                    ($a = new Change\LocalChange(
                        new Change\FileChange('/foo', '/foo'),
                        $this->createLineRemovedChange(3)
                    )),
                    ($b = new Change\LocalChange(
                        new Change\FileChange('/bar', '/bar'),
                        $this->createLineRemovedChange(3)
                    )),
                )
            )
        );

        $this->assertIteratorEqualsArray(
            array($b, $a),
            $sortIterator
        );
    }

    protected function assertIteratorEqualsArray(array $array, \Traversable $iterator)
    {
        $this->assertSame(
            $array,
            iterator_to_array($iterator)
        );
    }

    protected function getDiffIteratorMock(array $changes)
    {
        $iteratorMock = $this->getMockBuilder('Qafoo\\ChangeTrack\\Analyzer\\ChangeSet\\Diff\\DiffIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $iteratorMock->expects($this->any())
            ->method('getIterator')
            ->will(
                $this->returnValue(
                    new \ArrayIterator($changes)
                )
            );

        return $iteratorMock;
    }

    protected function createLineRemovedChange($lineNo)
    {
        return new Change\LineRemovedChange(
            $this->reflectionLookupMock,
            $lineNo
        );
    }

    protected function createLineAddedChange($lineNo)
    {
        return new Change\LineAddedChange(
            $this->reflectionLookupMock,
            $lineNo
        );
    }
}
