<?php

namespace Qafoo\ChangeTrack\Analyzer\Checkout;

use Arbit\VCSWrapper\Diff\Collection as VcsDiffCollection;
use Arbit\VCSWrapper\Diff\Chunk as VcsDiffChunk;
use Arbit\VCSWrapper\Diff\Line as VcsDiffLine;

use Qafoo\ChangeTrack\Analyzer\Diff\Diff;
use Qafoo\ChangeTrack\Analyzer\Diff\Chunk;
use Qafoo\ChangeTrack\Analyzer\Diff\Line;

class VcsWrapperDiffMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMapDiff()
    {
        $input = $this->createInput();

        $expectedOutput = $this->createExpectedOutput();

        $mapper = new VcsWrapperDiffMapper();
        $actualOutput = $mapper->mapDiffs($input);

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    private function createInput()
    {
        return array(
            new VcsDiffCollection(
                'foo.txt',
                'bar.txt',
                array(
                    new VcsDiffChunk(
                        1,
                        11,
                        2,
                        22,
                        array(
                            new VcsDiffLine(VcsDiffLine::ADDED, 'content'),
                        )
                    )
                )
            )
        );
    }

    private function createExpectedOutput()
    {
        return array(
            new Diff(
                'foo.txt',
                'bar.txt',
                array(
                    new Chunk(
                        1,
                        11,
                        2,
                        22,
                        array(
                            new Line(Line::ADDED, 'content'),
                        )
                    )
                )
            )
        );
    }
}
