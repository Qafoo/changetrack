<?php

namespace Qafoo\ChangeTrack\Analyzer\LineFeed;

use Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff\LineChangeFeed\ChunkLineFeedGenerator;
use Arbit\VCSWrapper\Diff;

/**
 * @group integration
 */
class ChunkLineFeedGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteratePureAddedDiff()
    {
        $parser = new Diff\Unified();
        $diff = $parser->parseString(file_get_contents(__DIR__ . '/_fixtures/diff_add_only.diff'));

        $lineFeedGenerator = new ChunkLineFeedGenerator($diff[0]->chunks[0]);

        $this->assertLinesFed(
            array(21, 22),
            $lineFeedGenerator
        );
    }

    public function testIteratePureRemovedDiff()
    {
        $diff = $this->loadDiff('diff_remove_only.diff');

        $lineFeedGenerator = new ChunkLineFeedGenerator($diff[0]->chunks[0]);

        $this->assertLinesFed(
            array(22, 22),
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

        $lineFeedGenerator = new ChunkLineFeedGenerator($diff[0]->chunks[0]);

        $this->assertLinesFed(
            array(22, 22),
            $lineFeedGenerator
        );
    }

    /**
     * @depends testIterateAddedAndRemovedDiff
     */
    public function testIterateComplexChangeDiff()
    {
        $diff = $this->loadDiff('diff_complex.diff');

        $lineFeedGenerator = new ChunkLineFeedGenerator($diff[0]->chunks[0]);
        $this->assertLinesFed(
            array(42, 42, 43, 48, 48, 53, 53, 53),
            $lineFeedGenerator
        );

        $lineFeedGenerator = new ChunkLineFeedGenerator($diff[0]->chunks[1]);
        $this->assertLinesFed(
            array(61, 62),
            $lineFeedGenerator
        );

        $lineFeedGenerator = new ChunkLineFeedGenerator($diff[0]->chunks[2]);
        $this->assertLinesFed(
            array(95, 95),
            $lineFeedGenerator
        );

        $lineFeedGenerator = new ChunkLineFeedGenerator($diff[0]->chunks[3]);
        $this->assertLinesFed(
            array(103, 103, 106, 106, 109, 109, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 113, 113, 113, 113, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 125, 125, 125, 125),
            $lineFeedGenerator
        );
    }

    private function assertLinesFed($expectedLines, ChunkLineFeedGenerator $lineFeedGenerator)
    {
        $fedLines = array();
        foreach ($lineFeedGenerator as $change) {
            $fedLines[] = $change->affectedLine;
        }

        $this->assertEquals(
            $expectedLines,
            $fedLines
        );
    }

    private function loadDiff($fileName)
    {
        $parser = new Diff\Unified();
        $diff = $parser->parseString(file_get_contents(__DIR__ . '/_fixtures/' . $fileName));
        return $diff;
    }
}
