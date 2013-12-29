<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\FISCalculator\MethodItem;
use Qafoo\ChangeTrack\FISCalculator\Set;
use Qafoo\ChangeTrack\FISCalculator\MutableSet;
use Qafoo\ChangeTrack\FISCalculator\FrequentItemSet;
use Qafoo\ChangeTrack\FISCalculator\TransactionDatabase;
use Qafoo\ChangeTrack\FISCalculator\TransactionDatabaseFactory;
use Qafoo\ChangeTrack\FISCalculator\AprioriGenerator;
use Qafoo\ChangeTrack\Analyzer\Result;

class FISCalculator
{
    /**
     * Calculates item sets with $minSupport on method changes in $analysisResult
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Result $analysisResult
     * @param float $minSupport
     * @return \Qafoo\ChangeTrack\FISCalculator\FrequentItemSet[]
     */
    public function calculateFrequentItemSets(Result $analysisResult, $minSupport)
    {
        $databaseFactory = new TransactionDataBaseFactory();
        $transactionBase = $databaseFactory->createDatabase($analysisResult);
        $aprioriGen = new AprioriGenerator();

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
            $currentItemSets = $aprioriGen->aprioriGen($setsForCandidates->getImmutable());
        }
        return $frequentItemSets->getArrayCopy();
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
