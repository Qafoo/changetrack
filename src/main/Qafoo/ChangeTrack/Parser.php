<?php

namespace Qafoo\ChangeTrack;

abstract class Parser
{
    /**
     * @param string $inputString
     * @return Qafoo\ChangeTrack\Analyzer\Result
     */
    abstract public function parseAnalysisResult($inputString);
}
