<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class AprioriGenerator
{
    /**
     * Generates candidate frequent item sets based on the Apriorigen algorithm
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set[] $inItemSets
     * @return \Qafoo\ChangeTrack\FISCalculator\Set[]
     */
    public function aprioriGen(Set $inItemSets)
    {
        $candidateSets = new Set(array());
        foreach ($inItemSets as $firstItemSet) {
            $candidateSets = $candidateSets->merge(
                $this->generateCandidatesWith($firstItemSet, $inItemSets)
            );
        }
        return $candidateSets;
    }

    /**
     * Generates a set of candidates on basis of $baseItemSet combined with
     * $inItemSets
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $baseItemSet
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $inItemSets;
     * @return \Qafoo\ChangeTrack\FISCalculator\Set[]
     */
    private function generateCandidatesWith(Set $baseItemSet, Set $inItemSets)
    {
        $candidateSets = new MutableSet();
        foreach ($inItemSets->without($baseItemSet) as $combineItemSet) {
            if (count($baseItemSet->intersect($combineItemSet)) == count($baseItemSet) - 1) {
                $candidateSet = $baseItemSet->merge($combineItemSet);

                if ($this->candidateHolds($candidateSet, $inItemSets)) {
                    $candidateSets->add($candidateSet);
                }
            }
        }
        return $candidateSets->getImmutable();
    }

    /**
     * Checks if all subsets of $candidateSet are contained in $inItemSets
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $candidateSet
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $inItemSets
     * @return bool
     */
    private function candidateHolds(Set $candidateSet, Set $inItemSets)
    {
        $candidateHolds = true;
        foreach ($candidateSet->createNMinusOnePermutationSets() as $subset) {
            if ( ! $inItemSets->contains($subset)) {
                return false;
            }
        }
        return true;
    }
}
