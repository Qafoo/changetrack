<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class FrequentItemSetCollection
{
    /**
     * @var \Qafoo\ChangeTrack\FISCalculator\FrequentItemSets[]
     */
    private $frequentItemSets;

    /**
     * @param \Qafoo\ChangeTrack\FISCalculator\FrequentItemSets[] $frequentItemSets
     */
    public function __construct(array $frequentItemSets)
    {
        $this->frequentItemSets = $frequentItemSets;
    }

    /**
     * @return \Qafoo\ChangeTrack\FISCalculator\FrequentItemSets[]
     */
    public function getFrequentItemSets()
    {
        return $this->frequentItemSets;
    }
}
