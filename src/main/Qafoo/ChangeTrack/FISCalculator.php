<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\FISCalculator\Set;
use Qafoo\ChangeTrack\FISCalculator\MutableSet;
use Qafoo\ChangeTrack\FISCalculator\FrequentItemSet;
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

        $frequentItemSets = new MutableSet();

        $currentItemSets = $this->calculateOneItemSets($transactionBase, $minSupport);
        while (count($currentItemSets) > 0) {
            $setsForCandidates = new MutableSet();
            foreach ($currentItemSets as $itemSet) {
                $currentSupport = $this->support($itemSet, $transactionBase);
                if ($currentSupport >= $minSupport) {
                    $frequentItemSets->add(new FrequentItemSet($itemSet, $currentSupport));
                    $setsForCandidates->add($itemSet);
                }
            }
            $currentItemSets = $this->aprioriGen($setsForCandidates->getImmutable());
        }
        return $frequentItemSets->getArrayCopy();
    }

    /**
     * Generates candidate frequent item sets based on the Apriorigen algorithm
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set[] $inItemSets
     * @return \Qafoo\ChangeTrack\FISCalculator\Set[]
     */
    private function aprioriGen(Set $inItemSets)
    {
        $candidateSets = new MutableSet();
        foreach ($inItemSets as $firstItemSet) {
            foreach ($inItemSets as $secondItemSet) {
                if ($firstItemSet->equals($secondItemSet)) {
                    continue;
                }

                if (count($firstItemSet->intersect($secondItemSet)) == count($firstItemSet) - 1) {
                    $candidateSet = $firstItemSet->merge($secondItemSet);

                    $candidateHolds = true;
                    foreach ($candidateSet->createNMinusOnePermutationSets() as $subset) {
                        if ( ! $inItemSets->contains($subset)) {
                            $candidateHolds = false;
                            break;
                        }
                    }

                    if ($candidateHolds) {
                        $candidateSets->add($candidateSet);
                    }
                }
            }
        }
        return $candidateSets->getImmutable();
    }

    private function calculateOneItemSets(array $transactionBase, $minSupport)
    {
        $firstTransaction = reset($transactionBase);
        $items = array_keys($firstTransaction);

        $oneItemSets = array();
        foreach ($items as $item) {
            $itemSet = new Set(array($item));
            if ($this->support($itemSet, $transactionBase) >= $minSupport) {
                $oneItemSets[] = $itemSet;
            }
        }
        return $oneItemSets;
    }

    /**
     * Calculates the support for $itemSet in $transactionBase
     *
     * @param array $itemSet
     * @param array $transactionBase
     * @return float
     */
    private function support(Set $itemSet, array $transactionBase)
    {
        $occurrences = 0;
        foreach ($transactionBase as $revision => $transaction) {
            foreach ($itemSet as $item) {
                if ( ! $transaction[$item]) {
                    continue 2;
                }
            }
            $occurrences++;
        }
        return $occurrences / count($transactionBase);
    }

    /**
     * Creates a transaction database from $analysisResult
     *
     * @param Result $analysisResult
     * @return array
     */
    private function createTransactionBase(Result $analysisResult)
    {
        $transactionBase = array();
        $items = array();

        foreach ($analysisResult->revisionChanges as $revisionChange) {
            foreach ($revisionChange->packageChanges as $packageChange) {
                foreach ($packageChange->classChanges as $classChange) {
                    foreach ($classChange->methodChanges as $methodChange) {

                        $revision = $revisionChange->revision;

                        if (!isset($transactionBase[$revision])) {
                            $transactionBase[$revision] = array();
                        }

                        $item = sprintf(
                            '%s::%s::%s',
                            $packageChange->packageName,
                            $classChange->className,
                            $methodChange->methodName
                        );

                        $items[$item] = true;
                        $transactionBase[$revision][$item] = true;
                    }
                }
            }
        }

        $items = array_keys($items);

        return $this->completeTransactionBase($transactionBase, $items);
    }

    /**
     * Complete the given sparse $transactionBase
     *
     * @param array $transactionBase
     * @param array $items
     * @return array
     */
    private function completeTransactionBase(array $transactionBase, array $items)
    {
        sort($items);

        $completedTransactionBase = array();

        foreach ($transactionBase as $revision => $transaction) {
            $completedTransactionBase[$revision] = array_fill_keys($items, false);
            foreach ($transaction as $item => $dummy) {
                $completedTransactionBase[$revision][$item] = true;
            }
        }

        return $completedTransactionBase;
    }
}
