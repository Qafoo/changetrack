<?php

namespace Qafoo\ChangeTrack\Analyzer;

abstract class LineFeedGenerator
{
    /**
     * Returns a Generator that fieds lines
     *
     * @return Generator<int>
     */
    abstract public function feedLines();
}
