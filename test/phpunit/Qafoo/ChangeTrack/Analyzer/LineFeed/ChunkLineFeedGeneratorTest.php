<?php

namespace Qafoo\ChangeTrack\Analyzer\LineFeed;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed\ChunkLineFeedGenerator;
use Qafoo\ChangeTrack\Analyzer\Change\LineAddedChange;
use Qafoo\ChangeTrack\Analyzer\Change\LineRemovedChange;
use Arbit\VCSWrapper\Diff;

/**
 * @group integration
 */
class ChunkLineFeedGeneratorTest extends \PHPUnit_Framework_TestCase
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

    private function createLineFeedGenerator($diff)
    {
        return new ChunkLineFeedGenerator($this->reflectionLookupMock, $diff);
    }

    private function expectedAddedChange($lineNo)
    {
        return new LineAddedChange($this->reflectionLookupMock, $lineNo);
    }

    private function expectedRemovedChange($lineNo)
    {
        return new LineRemovedChange($this->reflectionLookupMock, $lineNo);
    }

    public function testIteratePureAddedDiff()
    {
        $parser = new Diff\Unified();
        $diff = $parser->parseString(file_get_contents(__DIR__ . '/_fixtures/diff_add_only.diff'));

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[0]);

        $this->assertChangesFed(
            array(
                $this->expectedAddedChange(21),
                $this->expectedAddedChange(22),
            ),
            $lineFeedGenerator
        );
    }

    public function testIteratePureRemovedDiff()
    {
        $diff = $this->loadDiff('diff_remove_only.diff');

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[0]);

        $this->assertChangesFed(
            array(
                $this->expectedRemovedChange(22),
                $this->expectedRemovedChange(23),
            ),
            $lineFeedGenerator
        );
    }

    /**
     * @test testIteratePureAddedDiff
     * @test testIteratePureRemovedDiff
     */
    public function testIterateAddedAndRemovedDiff()
    {
        $diff = $this->loadDiff('diff_add_and_remove.diff');

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[0]);

        $this->assertChangesFed(
            array(
                $this->expectedRemovedChange(22),
                $this->expectedAddedChange(22),
            ),
            $lineFeedGenerator
        );
    }

    /**
     * @depends testIterateAddedAndRemovedDiff
     */
    public function testIterateComplexChangeDiff()
    {
        $diff = $this->loadDiff('diff_complex.diff');

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[0]);
        $this->assertChangesFed(
            array(
                $this->expectedRemovedChange(42),
                $this->expectedAddedChange(42),
                $this->expectedAddedChange(43),
                $this->expectedRemovedChange(47),
                $this->expectedAddedChange(48),
                $this->expectedRemovedChange(52),
                $this->expectedRemovedChange(53),
                $this->expectedAddedChange(53),
            ),
            $lineFeedGenerator
        );

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[1]);
        $this->assertChangesFed(
            array(
                $this->expectedAddedChange(61),
                $this->expectedAddedChange(62),
            ),
            $lineFeedGenerator
        );

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[2]);
        $this->assertChangesFed(
            array(
                $this->expectedRemovedChange(93),
                $this->expectedAddedChange(95)
            ),
            $lineFeedGenerator
        );

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[3]);
        $this->assertChangesFed(
            array(
                $this->expectedRemovedChange(101),
                $this->expectedAddedChange(103),
                $this->expectedRemovedChange(104),
                $this->expectedAddedChange(106),
                $this->expectedRemovedChange(107),
                $this->expectedAddedChange(109),
                $this->expectedRemovedChange(109),
                $this->expectedRemovedChange(110),
                $this->expectedRemovedChange(111),
                $this->expectedRemovedChange(112),
                $this->expectedRemovedChange(113),
                $this->expectedRemovedChange(114),
                $this->expectedRemovedChange(115),
                $this->expectedRemovedChange(116),
                $this->expectedRemovedChange(117),
                $this->expectedRemovedChange(118),
                $this->expectedRemovedChange(119),
                $this->expectedRemovedChange(120),
                $this->expectedRemovedChange(121),
                $this->expectedAddedChange(111),
                $this->expectedRemovedChange(123),
                $this->expectedRemovedChange(124),
                $this->expectedRemovedChange(125),
                $this->expectedRemovedChange(126),
                $this->expectedAddedChange(113),
                $this->expectedAddedChange(114),
                $this->expectedAddedChange(115),
                $this->expectedAddedChange(116),
                $this->expectedAddedChange(117),
                $this->expectedAddedChange(118),
                $this->expectedAddedChange(119),
                $this->expectedAddedChange(120),
                $this->expectedAddedChange(121),
                $this->expectedAddedChange(122),
                $this->expectedAddedChange(123),
                $this->expectedRemovedChange(128),
                $this->expectedRemovedChange(129),
                $this->expectedRemovedChange(130),
                $this->expectedAddedChange(125),
            ),
            $lineFeedGenerator
        );
    }

    private function assertChangesFed($expectedChanges, ChunkLineFeedGenerator $lineFeedGenerator)
    {
        $fedChanges = array();
        foreach ($lineFeedGenerator as $change) {
            $fedChanges[] = $change;
        }

        $this->assertEquals(
            $expectedChanges,
            $fedChanges
        );
    }

    private function loadDiff($fileName)
    {
        $parser = new Diff\Unified();
        $diff = $parser->parseString(file_get_contents(__DIR__ . '/_fixtures/' . $fileName));
        return $diff;
    }
}
