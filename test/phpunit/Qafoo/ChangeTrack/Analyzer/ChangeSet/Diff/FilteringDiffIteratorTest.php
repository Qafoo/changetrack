<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\Change\FileChange;
use Qafoo\ChangeTrack\Analyzer\Change\LineAddedChange;
use Qafoo\ChangeTrack\Analyzer\Change\LocalChange;

class FilteringDiffIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMatchingTests
     */
    public function testMatches($path, array $patterns, $expectedOutcome)
    {
        $filter = new FilteringDiffIterator($this->createDummyIterator(array($path)), $patterns);
        $this->assertSame($expectedOutcome, count(iterator_to_array($filter)) > 0);
    }

    public function getMatchingTests()
    {
        return array(
            array('foo/bar', array('foo/*'), true),
            array('foo/bar', array('foo'), false)
        );
    }

    /**
     * @dataProvider getFilteringTests
     */
    public function testIsFiltered($path, array $includedPaths, array $excludedPaths, $expectedOutcome)
    {
        $filter = new FilteringDiffIterator($this->createDummyIterator(array($path)), $includedPaths, $excludedPaths);
        $this->assertSame($expectedOutcome, count(iterator_to_array($filter)) === 0);
    }

    public function getFilteringTests()
    {
        return array(
            array('foo', array(), array(), false),
            array('foo', array('foo'), array('foo/bar'), false),
            array('foo/bar', array('foo/*'), array('foo/bar'), true),
        );
    }

    private function createDummyIterator(array $changedPaths)
    {
        foreach ($changedPaths as $path) {
            yield new LocalChange(new FileChange(null, $path), new LineAddedChange(0));
        }
    }
}