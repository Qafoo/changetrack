<?php

namespace Qafoo\ChangeTrack;

abstract class Renderer
{
    /**
     * Render the output of $analysisResult into a string and return it.
     *
     * @param array $analysisResult
     * @return string
     */
    abstract public function renderOutput(array $analysisResult);
}
