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
}
