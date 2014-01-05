<?php

namespace Qafoo\ChangeTrack\Analyzer\Checkout;

use Arbit\VCSWrapper\Diff\Collection as VcsDiffCollection;
use Arbit\VCSWrapper\Diff\Chunk as VcsDiffChunk;
use Arbit\VCSWrapper\Diff\Line as VcsDiffLine;

use Qafoo\ChangeTrack\Analyzer\Diff\Diff;
use Qafoo\ChangeTrack\Analyzer\Diff\Chunk;
use Qafoo\ChangeTrack\Analyzer\Diff\Line;

class VcsWrapperDiffMapper
{
    /**
     * @var array
     */
    private $typeMap = array(
        VcsDiffLine::ADDED => Line::ADDED,
        VcsDiffLine::REMOVED => Line::REMOVED,
        VcsDiffLine::UNCHANGED => Line::UNCHANGED,
    );

    /**
     * Maps a VCSWrapper Diff to the ChangeTrack diff representation.
     *
     * @param \Arbit\VCSWrapper\Diff\Collection[] $vcsDiffs
     * @return \Qafoo\ChangeTrack\Analyzer\Diff\Diff[]
     */
    public function mapDiffs(array $vcsDiffs)
    {
        $mappedDiffs = array();
        foreach ($vcsDiffs as $vcsDiff) {
            $mappedDiffs[] = $this->mapDiff($vcsDiff);
        }
        return $mappedDiffs;
    }

    /**
     * @param \Arbit\VCSWrapper\Diff\Collection $vcsDiff;
     * @return \Qafoo\ChangeTrack\Analyzer\Diff\Diff
     */
    private function mapDiff(VcsDiffCollection $vcsDiff)
    {
        return new Diff(
            $vcsDiff->from,
            $vcsDiff->to,
            $this->mapChunks($vcsDiff->chunks)
        );
    }

    /**
     * @param \Arbit\VCSWrapper\Diff\Chunk[] $vcsChunks
     * @return \Qafoo\ChangeTrack\Analyzer\Diff\Chunk[]
     */
    private function mapChunks(array $vcsChunks)
    {
        $mappedChunks = array();
        foreach ($vcsChunks as $vcsChunk) {
            $mappedChunks[] = $this->mapChunk($vcsChunk);
        }
        return $mappedChunks;
    }

    /**
     * @param \Arbit\VCSWrapper\Diff\Chunk $vcsChunk
     * @return \Qafoo\ChangeTrack\Analyzer\Diff\Chunk
     */
    private function mapChunk(VcsDiffChunk $vcsChunk)
    {
        return new Chunk(
            $vcsChunk->start,
            $vcsChunk->startRange,
            $vcsChunk->end,
            $vcsChunk->endRange,
            $this->mapLines($vcsChunk->lines)
        );
    }

    /**
     * @param \Arbit\VCSWrapper\Diff\Line[] $vcsLines
     * @return \Qafoo\ChangeTrack\Analyzer\Diff\Line[]
     */
    private function mapLines(array $vcsLines)
    {
        $mappedLines = array();
        foreach ($vcsLines as $vcsLine) {
            $mappedLines[] = $this->mapLine($vcsLine);
        }
        return $mappedLines;
    }

    /**
     * @param \Arbit\VCSWrapper\Diff\Line $vcsLine
     * @return \Qafoo\ChangeTrack\Analyzer\Diff\Line
     */
    private function mapLine(VcsDiffLine $vcsLine)
    {
        return new Line(
            $this->mapType($vcsLine->type),
            $vcsLine->content
        );
    }

    /**
     * @param const $vcsType
     * @return const
     */
    private function mapType($vcsType)
    {
        return $this->typeMap[$vcsType];
    }
}
