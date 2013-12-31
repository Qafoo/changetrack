<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class FrequentItemSet implements Item
{
    /**
     * @var \Qafoo\ChangeTrack\FISCalculator\Set
     */
    private $itemSet;

    /**
     * @var float
     */
    private $support;

    /**
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $itemSet
     * @param float $support
     */
    public function __construct(Set $itemSet, $support)
    {
        $this->itemSet = $itemSet;
        $this->support = $support;
    }

    /**
     * @return \Qafoo\ChangeTrack\FISCalculator\Set
     */
    public function getItemSet()
    {
        return $this->itemSet;
    }

    /**
     * @return float
     */
    public function getSupport()
    {
        return $this->support;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return sprintf('%s (%01.2f)', (string) $this->itemSet, $this->support);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getHash();
    }
}
