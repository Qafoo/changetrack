<?php

namespace Qafoo\ChangeTrack\Analyzer;

class ChangeRecorderFactory
{
    /**
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeRecorder $resultBuilder
     */
    public function createChangeRecorder(ResultBuilder $resultBuilder)
    {
        return new ChangeRecorder($resultBuilder);
    }
}
