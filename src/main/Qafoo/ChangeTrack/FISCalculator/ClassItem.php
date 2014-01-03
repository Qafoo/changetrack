<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class ClassItem extends RegularItem
{
    /**
     * @var string
     */
    private $package;

    /**
     * @var string
     */
    private $class;

    /**
     * @param string $package
     * @param string $class
     */
    public function __construct($package, $class)
    {
        $this->package = $package;
        $this->class = $class;
    }

    /**
     * Returns a hash that uniquely identifies the item.
     *
     * @return string
     */
    public function getHash()
    {
        return sprintf(
            '%s::%s',
            $this->package,
            $this->class
        );
    }

    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
