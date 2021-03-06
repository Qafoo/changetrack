<?php

namespace Qafoo\ChangeTrack\FISCalculator;

interface Item
{
    /**
     * Returns a hash that uniquely identifies the item.
     *
     * @return string
     */
    public function getHash();

    /**
     * @return string
     */
    public function __toString();
}
