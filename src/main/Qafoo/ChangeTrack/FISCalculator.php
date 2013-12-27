<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\FISCalculator\Set;
use Qafoo\ChangeTrack\FISCalculator\MutableSet;
use Qafoo\ChangeTrack\FISCalculator\FrequentItemSet;
use Qafoo\ChangeTrack\FISCalculator\TransactionDataBase;
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
        $transactionBase = $this->createTransactionBase($analysisResult);
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

    /**
     * Creates a transaction database from $analysisResult
     *
     * @param Result $analysisResult
     * @return \Qafoo\ChangeTrack\FISCalculator\TransactionDataBase
     */
    private function createTransactionBase(Result $analysisResult)
    {
        $transactionBase = new TransactionDataBase();

        foreach ($analysisResult->revisionChanges as $revisionChange) {
            foreach ($revisionChange->packageChanges as $packageChange) {
                foreach ($packageChange->classChanges as $classChange) {
                    foreach ($classChange->methodChanges as $methodChange) {

                        $revision = $revisionChange->revision;

                        $item = sprintf(
                            '%s::%s::%s',
                            $packageChange->packageName,
                            $classChange->className,
                            $methodChange->methodName
                        );

                        $transactionBase->addItem($revision, $item);
                    }
                }
            }
        }

        return $transactionBase;
    }
}
