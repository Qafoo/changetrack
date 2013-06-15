<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;

abstract class ChangeSet
{
    abstract public function recordChanges(ChangeRecorder $lineChangeRecorder);
}
