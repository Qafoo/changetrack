<?php

namespace Qafoo\ChangeTrack\Calculator;

abstract class Parser
{
    /**
     * @param string $inputString
     * @return Qafoo\ChangeTrack\Analyzer\Result
     */
    abstract public function parseAnalysisResult($inputString);
}
