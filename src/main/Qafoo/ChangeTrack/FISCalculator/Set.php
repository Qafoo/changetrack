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
    protected $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
        $this->ensureSetProperties();
    }

    /**
     * Ensures the items are unique and sorted.
     */
    protected function ensureSetProperties()
    {
        $this->items = array_unique($this->items);
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

    /**
     * Returns a new set which contains all items from this set which are also present in $otherSet.
     *
     * @param Set $otherSet
     */
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
        foreach (array_keys($this->items) as $id) {
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

    /**
     * Returns if $item is contained in the set.
     *
     * @param mixed $item
     * @return bool
     */
    public function contains($item)
    {
        foreach ($this->items as $containedItem) {
            if ($item == $containedItem) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a new set with all items except of $item
     *
     * @param mixed $item
     * @return \Qafoo\ChangeTrack\FISCalculator\Set
     */
    public function without($item)
    {
        $newItems = array();
        foreach ($this->items as $containedItem) {
            if ($item != $containedItem) {
                $newItems[] = $containedItem;
            }
        }
        return new Set($newItems);
    }

    /**
     * Returns a copy of the Set as an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(', ', $this->items);
    }
}
