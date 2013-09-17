<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Result;

abstract class Renderer
{
    /**
     * Render the output of $analysisResult into a string and return it.
     *
     * @param \Qafoo\ChangeTrack\AnalysisResult\Result $analysisResult
     * @return string
     */
    abstract public function renderOutput(Result $analysisResult);
}
