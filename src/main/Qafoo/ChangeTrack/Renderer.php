<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\Analyzer\Result;

abstract class Renderer
{
    /**
     * Render the output of $analysisResult into a string and return it.
     *
     * @param array $analysisResult
     * @return string
     */
    abstract public function renderOutput(Result $analysisResult);
}
