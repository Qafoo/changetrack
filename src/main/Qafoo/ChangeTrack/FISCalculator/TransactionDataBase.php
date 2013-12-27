<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class TransactionDataBase
{
    /**
     * Row based data set
     *
     * @var array
     */
    private $data;

    /**
     * Items that occur in this data base
     *
     * @var string[]
     */
    private $items = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        foreach ($data as $transactionIndex => $transaction) {
            foreach ($transaction as $item) {
                $this->addItem($transactionIndex, $item);
            }
        }
    }

    /**
     * Adds $item to the transaction with $transactionIndex
     *
     * @param string $transactionIndex
     * @param string $item
     */
    public function addItem($transactionIndex, $item)
    {
        if (!isset($this->data[$transactionIndex])) {
            $this->data[$transactionIndex] = array();
        }
        $this->data[$transactionIndex][$item] = true;
        $this->items[$item] = true;
    }

    /**
     * Calculates the support for $itemSet
     *
     * @param array $itemSet
     * @return float
     */
    public function support(Set $itemSet)
    {
        $occurrences = 0;
        foreach ($this->data as $transaction) {
            foreach ($itemSet as $item) {
                if (!isset($transaction[$item])) {
                    continue 2;
                }
            }
            $occurrences++;
        }
        return $occurrences / count($this->data);
    }

    /**
     * Returns the items used in this transaction data base.
     *
     * @return string[]
     */
    public function getItems()
    {
        return array_keys($this->items);
    }
}
