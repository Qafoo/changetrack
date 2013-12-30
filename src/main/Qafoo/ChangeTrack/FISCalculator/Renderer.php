<?php

namespace Qafoo\ChangeTrack\FISCalculator;

abstract class Renderer
{
    /**
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\FrequentItemSetCollection $frequentItemSets
     */
    abstract public function renderOutput(FrequentItemSetCollection $frequentItemSets);
}
