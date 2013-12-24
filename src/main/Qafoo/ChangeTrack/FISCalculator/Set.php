<?php

namespace Qafoo\ChangeTrack\FISCalculator;

use IteratorAggregate;
use Countable;
use ArrayIterator;

class Set implements IteratorAggregate, Countable
{
    /**
     * @var array
     */
    private $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = array_unique(array_values(($items)));
        sort($this->items);
    }

    /**
     * Returns if the current set contains the same elements as $otherSet
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $otherSet
     * @return bool
     */
    public function equals(Set $otherSet)
    {
        return ($this->items == $otherSet->items);
    }

    /**
     * Returns if the current set is a subset of $otherSet.
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $otherSet
     * @return bool
     */
    public function isSubSetOf(Set $otherSet)
    {
        return (array_intersect($this->items, $otherSet->items) == $this->items);
    }

    /**
     * Returns a new set that contains all items of this set and $otherSet
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $otherSet
     * @return \Qafoo\ChangeTrack\FISCalculator\Set
     */
    public function merge(Set $otherSet)
    {
        return new self(array_merge($this->items, $otherSet->items));
    }

    public function intersect(Set $otherSet)
    {
        return new self(array_intersect($this->items, $otherSet->items));
    }

    /**
     * Returns an array of n-1 permutations sets from the current set
     *
     * @return \Qafoo\ChangeTrack\FISCalculator\Set
     */
    public function createNMinusOnePermutationSets()
    {
        $permutationSets = array();
        foreach ($this->items as $id => $item) {
            $permutation = $this->items;
            unset($permutation[$id]);
            $permutationSets[] = new Set($permutation);
        }
        return $permutationSets;
    }

    /**
     * @return array
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }
}
