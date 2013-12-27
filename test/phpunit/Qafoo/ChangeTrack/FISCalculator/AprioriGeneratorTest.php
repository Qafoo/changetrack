<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class AprioriGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $inItemSets
     * @param \Qafoo\ChangeTrack\FISCalculator\Set $expectedCandidateSets
     * @dataProvider provideAprioriGenData
     */
    public function testAprioriGen(Set $inItemSets, Set $expectedCandidateSets)
    {
        $aprioriGen = new AprioriGenerator();

        $actualCandidateSets = $aprioriGen->aprioriGen($inItemSets);

        $this->assertTrue($expectedCandidateSets->equals($actualCandidateSets));
    }

    public function provideAprioriGenData()
    {
        return array(
            array(
                // Input
                new Set(
                    array(
                        new Set(array('A')),
                        new Set(array('B')),
                    )
                ),
                // Expected output
                new Set(
                    array(
                        new Set(array('A', 'B'))
                    )
                )
            ),
            array(
                // Input
                new Set(
                    array(
                        new Set(array('A', 'B')),
                        new Set(array('B', 'C')),
                        new Set(array('A', 'C')),
                    )
                ),
                // Expected output
                new Set(
                    array(
                        new Set(array('A', 'B', 'C'))
                    )
                )
            ),
            array(
                // Input
                new Set(
                    array(
                        new Set(array('A', 'B')),
                        new Set(array('B', 'C')),
                    )
                ),
                // Expected output
                // No candidate, since ('A', 'C') is not part of the input
                new Set(array())
            ),
        );
    }
}
