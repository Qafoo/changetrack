<?php

namespace Qafoo\ChangeTrack\FISCalculator;

abstract class Item
{
    /**
     * Returns a hash that uniquely identifies the item.
     *
     * @return string
     */
    abstract public function getHash();

    /**
     * @return string
     */
    final public function __toString()
    {
        return $this->getHash();
    }
}
