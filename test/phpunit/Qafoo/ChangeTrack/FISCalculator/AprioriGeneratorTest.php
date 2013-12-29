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
                        $this->stringSet('A'),
                        $this->stringSet('B'),
                    )
                ),
                // Expected output
                new Set(
                    array(
                        $this->stringSet('A', 'B'),
                    )
                )
            ),
            array(
                // Input
                new Set(
                    array(
                        $this->stringSet('A', 'B'),
                        $this->stringSet('B', 'C'),
                        $this->stringSet('A', 'C'),
                    )
                ),
                // Expected output
                new Set(
                    array(
                        $this->stringSet('A', 'B', 'C'),
                    )
                )
            ),
            array(
                // Input
                new Set(
                    array(
                        $this->stringSet('A', 'B'),
                        $this->stringSet('B', 'C'),
                    )
                ),
                // Expected output
                // No candidate, since ('A', 'C') is not part of the input
                new Set(array())
            ),
        );
    }

    /**
     * Creates a set of StringItem instances from a variable number of given
     * arguments.
     *
     * @return \Qafoo\ChangeTrack\FISCalculator\Set
     */
    protected function stringSet()
    {
        return new Set(
            array_map(
                function ($string) {
                    return new StringItem($string);
                },
                func_get_args()
            )
        );
    }
}
