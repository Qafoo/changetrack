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
        foreach ($inItemSets as $combineItemSet) {
            if ($baseItemSet->equals($combineItemSet)) {
                continue;
            }

            if (count($baseItemSet->intersect($combineItemSet)) == count($baseItemSet) - 1) {
                $candidateSet = $baseItemSet->merge($combineItemSet);

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
        return $candidateSets->getImmutable();
    }
}
