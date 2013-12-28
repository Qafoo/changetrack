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
            if ( ! $this->isPotentialCandidate($baseItemSet, $combineItemSet)) {
                continue;
            }

            $candidateSet = $baseItemSet->merge($combineItemSet);
            if ($this->candidateHolds($candidateSet, $inItemSets)) {
                $candidateSets->add($candidateSet);
            }
        }
        return $candidateSets->getImmutable();
    }

    /**
     * Checks if $baseItemSet can be combined with $combineItemSet to form a
     * candidate.
     *
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $baseItemSet
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $candidateSet
     * @return bool
     */
    private function isPotentialCandidate(Set $baseItemSet, Set $combineItemSet)
    {
        $insersectSet = $baseItemSet->intersect($combineItemSet);
        return (count($insersectSet) == count($baseItemSet) - 1);
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
        foreach ($candidateSet->createNMinusOnePermutationSets() as $subset) {
            if ( ! $inItemSets->contains($subset)) {
                return false;
            }
        }
        return true;
    }
}
