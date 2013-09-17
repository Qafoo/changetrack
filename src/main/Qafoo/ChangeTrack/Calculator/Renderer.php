<?php

namespace Qafoo\ChangeTrack\Calculator;

use Qafoo\ChangeTrack\Calculator\Stats;

abstract class Renderer
{
    /**
     *
     * @param \Qafoo\ChangeTrack\Calculator\Stats $statistics
     */
    abstract public function renderOutput(Stats $statistics);
}
