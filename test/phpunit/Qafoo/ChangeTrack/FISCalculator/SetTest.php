<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class SetTest extends \PHPUnit_Framework_TestCase
{
    public function testDifferentCreationOrderEquals()
    {
        $firstSet = new Set(array('A', 'C', 'B'));
        $secondSet = new Set(array('C', 'A', 'B'));

        $this->assertTrue($firstSet->equals($secondSet));
    }

    public function testIsSubsetOf()
    {
        $set = new Set(array('A', 'B', 'C'));
        $subSet = new Set(array('B', 'C'));

        $this->assertTrue($subSet->isSubSetOf($set));
    }

    public function testIsNotSubsetOf()
    {
        $notSubSet = new Set(array('A', 'B', 'C'));
        $set = new Set(array('B', 'C'));

        $this->assertFalse($notSubSet->isSubSetOf($set));
    }

    public function testMerge()
    {
        $firstSet = new Set(array('A', 'B'));
        $secondSet = new Set(array('B', 'C'));

        $this->assertTrue(
            $firstSet->merge($secondSet)->equals(new Set(array('A', 'B', 'C')))
        );
    }

    public function testCreateNMinusOnePermutationSets()
    {
        $set = new Set(array('A', 'B', 'C'));

        $expectedSets = array(
            new Set(array('A', 'B')),
            new Set(array('A', 'C')),
            new Set(array('B', 'C')),
        );

        $actualSets = $set->createNMinusOnePermutationSets();

        $this->assertSetCollectionsEqual($expectedSets, $actualSets);
    }

    private function assertSetCollectionsEqual(array $expectedSets, array $actualSets)
    {
        foreach ($actualSets as $actualIndex => $actualSet) {
            foreach ($expectedSets as $expectedIndex => $expectedSet) {
                if ($expectedSet->equals($actualSet)) {
                    unset($actualSets[$actualIndex]);
                    unset($expectedSets[$expectedIndex]);
                    break;
                }
            }
        }
        $this->assertCount(0, $expectedSets);
        $this->assertCount(0, $actualSets);
    }
}
