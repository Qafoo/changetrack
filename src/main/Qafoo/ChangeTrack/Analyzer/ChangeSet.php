<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\PathFilter;

abstract class ChangeSet
{
    abstract public function recordChanges(ChangeRecorder $lineChangeRecorder, PathFilter $pathFilter);
}
