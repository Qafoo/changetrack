<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class MutableSet extends Set
{
    /**
     * @param array $items
     */
    public function __construct(array $items = array())
    {
        parent::__construct($items);
    }

    /**
     * Adds $item to the set, if it is not contained yet.
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Item $item
     */
    public function add(Item $item)
    {
        parent::add($item);
        $this->ensureSetProperties();
    }

    /**
     * Returns an immutable copy of this set.
     *
     * @return \Qafoo\ChangeTrack\FISCalculator\Set
     */
    public function getImmutable()
    {
        return new Set($this->items);
    }
}
