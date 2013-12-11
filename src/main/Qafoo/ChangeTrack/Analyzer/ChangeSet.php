<?php

namespace Qafoo\ChangeTrack\Analyzer;

abstract class ChangeSet
{
    abstract public function recordChanges(ChangeRecorder $lineChangeRecorder, array $paths, array $excludedPaths);
}
