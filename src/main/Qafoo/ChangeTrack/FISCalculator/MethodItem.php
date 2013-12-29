<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class MethodItem extends Item
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
     * @var string
     */
    private $method;

    /**
     * @param string $package
     * @param string $class
     * @param string $method
     */
    public function __construct($package, $class, $method)
    {
        $this->package = $package;
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * Returns a hash that uniquely identifies the item.
     *
     * @return string
     */
    public function getHash()
    {
        return sprintf(
            '%s::%s::%s',
            $this->package,
            $this->class,
            $this->method
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

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
