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

    public function testIteratePureAddedDiff()
    {
        $parser = new Diff\Unified();
        $diff = $parser->parseString(file_get_contents(__DIR__ . '/_fixtures/diff_add_only.diff'));

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[0]);

        $this->assertChangesFed(
            array(
                new LineAddedChange(21),
                new LineAddedChange(22),
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
                new LineRemovedChange(22),
                new LineRemovedChange(23),
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
                new LineRemovedChange(22),
                new LineAddedChange(22),
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
                new LineRemovedChange(42),
                new LineAddedChange(42),
                new LineAddedChange(43),
                new LineRemovedChange(47),
                new LineAddedChange(48),
                new LineRemovedChange(52),
                new LineRemovedChange(53),
                new LineAddedChange(53),
            ),
            $lineFeedGenerator
        );

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[1]);
        $this->assertChangesFed(
            array(
                new LineAddedChange(61),
                new LineAddedChange(62),
            ),
            $lineFeedGenerator
        );

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[2]);
        $this->assertChangesFed(
            array(
                new LineRemovedChange(93),
                new LineAddedChange(95)
            ),
            $lineFeedGenerator
        );

        $lineFeedGenerator = $this->createLineFeedGenerator($diff[0]->chunks[3]);
        $this->assertChangesFed(
            array(
                new LineRemovedChange(101),
                new LineAddedChange(103),
                new LineRemovedChange(104),
                new LineAddedChange(106),
                new LineRemovedChange(107),
                new LineAddedChange(109),
                new LineRemovedChange(109),
                new LineRemovedChange(110),
                new LineRemovedChange(111),
                new LineRemovedChange(112),
                new LineRemovedChange(113),
                new LineRemovedChange(114),
                new LineRemovedChange(115),
                new LineRemovedChange(116),
                new LineRemovedChange(117),
                new LineRemovedChange(118),
                new LineRemovedChange(119),
                new LineRemovedChange(120),
                new LineRemovedChange(121),
                new LineAddedChange(111),
                new LineRemovedChange(123),
                new LineRemovedChange(124),
                new LineRemovedChange(125),
                new LineRemovedChange(126),
                new LineAddedChange(113),
                new LineAddedChange(114),
                new LineAddedChange(115),
                new LineAddedChange(116),
                new LineAddedChange(117),
                new LineAddedChange(118),
                new LineAddedChange(119),
                new LineAddedChange(120),
                new LineAddedChange(121),
                new LineAddedChange(122),
                new LineAddedChange(123),
                new LineRemovedChange(128),
                new LineRemovedChange(129),
                new LineRemovedChange(130),
                new LineAddedChange(125),
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
