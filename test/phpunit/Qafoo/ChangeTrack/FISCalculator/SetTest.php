<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class SetTest extends \PHPUnit_Framework_TestCase
{
    public function testDifferentCreationOrderEquals()
    {
        $firstSet = new Set(array(new StringItem('A'), new StringItem('C'), new StringItem('B')));
        $secondSet = new Set(array(new StringItem('C'), new StringItem('A'), new StringItem('B')));

        $this->assertTrue($firstSet->equals($secondSet));
    }

    public function testIsSubsetOf()
    {
        $set = new Set(array(new StringItem('A'), new StringItem('B'), new StringItem('C')));
        $subSet = new Set(array(new StringItem('B'), new StringItem('C')));

        $this->assertTrue($subSet->isSubSetOf($set));
    }

    public function testIsNotSubsetOf()
    {
        $notSubSet = new Set(array(new StringItem('A'), new StringItem('B'), new StringItem('C')));
        $set = new Set(array(new StringItem('B'), new StringItem('C')));

        $this->assertFalse($notSubSet->isSubSetOf($set));
    }

    public function testMerge()
    {
        $firstSet = new Set(array(new StringItem('A'), new StringItem('B')));
        $secondSet = new Set(array(new StringItem('B'), new StringItem('C')));

        $this->assertTrue(
            $firstSet->merge($secondSet)->equals(
                new Set(array(new StringItem('A'), new StringItem('B'), new StringItem('C')))
            )
        );
    }

    public function testIntersect()
    {
        $firstSet = new Set(array(new StringItem('A'), new StringItem('B'), new StringItem('C')));
        $secondSet = new Set(array(new StringItem('A'), new StringItem('C'), new StringItem('D')));

        $expectedSet = new Set(array(new StringItem('A'), new StringItem('C')));

        $this->assertTrue(
            $firstSet->intersect($secondSet)->equals($expectedSet)
        );
    }

    public function testCreateNMinusOnePermutationSets()
    {
        $set = new Set(array(new StringItem('A'), new StringItem('B'), new StringItem('C')));

        $expectedSets = array(
            new Set(array(new StringItem('A'), new StringItem('B'))),
            new Set(array(new StringItem('A'), new StringItem('C'))),
            new Set(array(new StringItem('B'), new StringItem('C'))),
        );

        $actualSets = $set->createNMinusOnePermutationSets();

        $this->assertSetCollectionsEqual($expectedSets, $actualSets);
    }

    public function testWithout()
    {
        $set = new Set(array(new StringItem('A'), new StringItem('B'), new StringItem('C')));

        $this->assertEquals(
            new Set(array(new StringItem('A'), new StringItem('C'))),
            $set->without(new StringItem('B'))
        );
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
