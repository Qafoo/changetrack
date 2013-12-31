<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class StringItem extends RegularItem
{
    /**
     * @var string
     */
    private $string;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * Returns a hash that uniquely identifies the item.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->string;
    }
}
