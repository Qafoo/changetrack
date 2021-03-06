<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\FISCalculator\MethodItem;
use Qafoo\ChangeTrack\FISCalculator\Set;
use Qafoo\ChangeTrack\FISCalculator\MutableSet;
use Qafoo\ChangeTrack\FISCalculator\FrequentItemSet;
use Qafoo\ChangeTrack\FISCalculator\FrequentItemSetCollection;
use Qafoo\ChangeTrack\FISCalculator\TransactionDatabase;
use Qafoo\ChangeTrack\FISCalculator\AprioriGenerator;
use Qafoo\ChangeTrack\Analyzer\Result;

class FISCalculator
{
    /**
     * @var \Qafoo\ChangeTrack\FISCalculator\AprioriGenerator
     */
    private $aprioriGen;

    /**
     * @param \Qafoo\ChangeTrack\FISCalculator\AprioriGenerator $aprioriGen
     */
    public function __construct(AprioriGenerator $aprioriGen)
    {
        $this->aprioriGen = $aprioriGen;
    }

    /**
     * Calculates item sets with $minSupport on method changes in $analysisResult
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\TransactionDatabase $analysisResult
     * @param float $minSupport
     * @return \Qafoo\ChangeTrack\FISCalculator\FrequentItemSetCollection
     */
    public function calculateFrequentItemSets(TransactionDatabase $transactionBase, $minSupport)
    {
        $frequentItemSets = new MutableSet();

        $currentItemSets = $this->calculateOneItemSets($transactionBase, $minSupport);
        while (count($currentItemSets) > 0) {
            $setsForCandidates = new MutableSet();
            foreach ($currentItemSets as $itemSet) {
                $currentSupport = $transactionBase->support($itemSet);
                if ($currentSupport >= $minSupport) {
                    $frequentItemSets->add(new FrequentItemSet($itemSet, $currentSupport));
                    $setsForCandidates->add($itemSet);
                }
            }
            $currentItemSets = $this->aprioriGen->aprioriGen($setsForCandidates->getImmutable());
        }
        return new FrequentItemSetCollection($frequentItemSets->getArrayCopy());
    }

    /**
     * Calculate all item sets with only a single item that have $minSupport
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\TransactionDataBase $transactionBase
     * @param float $minSupport
     * @return \Qafoo\ChangeTrack\FISCalculator\Set[]
     */
    private function calculateOneItemSets(TransactionDataBase $transactionBase, $minSupport)
    {
        $items = $transactionBase->getItems();

        $oneItemSets = array();
        foreach ($items as $item) {
            $itemSet = new Set(array($item));
            if ($transactionBase->support($itemSet) >= $minSupport) {
                $oneItemSets[] = $itemSet;
            }
        }
        return $oneItemSets;
    }
}
