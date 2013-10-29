<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\Change;

class SortingDiffIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testSortChangesByType()
    {
        $sortIterator = new SortingDiffIterator(
            $this->getDiffIteratorMock(
                array(
                    ($a = new Change\LocalChange(
                        new Change\FileChange('/foo', '/foo'),
                        new Change\LineRemovedChange(3)
                    )),
                    ($b = new Change\LocalChange(
                        new Change\FileChange('/foo', '/foo'),
                        new Change\LineAddedChange(3)
                    )),
                    ($c = new Change\LocalChange(
                        new Change\FileChange('/foo', '/foo'),
                        new Change\LineRemovedChange(4)
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
                        new Change\LineRemovedChange(3)
                    )),
                    ($b = new Change\LocalChange(
                        new Change\FileChange('/bar', '/bar'),
                        new Change\LineRemovedChange(3)
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
}
