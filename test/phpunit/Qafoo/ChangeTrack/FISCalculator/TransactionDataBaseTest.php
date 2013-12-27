<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class TransactionDataBaseTest extends \PHPUnit_Framework_TestCase
{
    public function testItemsRegisteredDuringConstruction()
    {
        $database = new TransactionDataBase(
            array(
                '1' => array('A', 'C'),
                '2' => array('B', 'C'),
            )
        );

        $this->assertEquals(
            array('A', 'C', 'B'),
            $database->getItems()
        );
    }

    public function testItemsRegisteredOnAdd()
    {
        $database = new TransactionDataBase();

        $database->addItem('1', 'A');
        $database->addItem('1', 'C');
        $database->addItem('2', 'B');
        $database->addItem('2', 'C');

        $this->assertEquals(
            array('A', 'C', 'B'),
            $database->getItems()
        );
    }

    /**
     * @param string[] $items
     * @param float $expectedSupport
     * @dataProvider provideSupportData
     */
    public function testSupport(array $items, $expectedSupport)
    {
        $database = new TransactionDataBase(
            array(
                '1' => array('A', 'C'),
                '2' => array('A'),
                '3' => array('A', 'B'),
                '4' => array('A', 'C')
            )
        );

        $this->assertEquals(
            $expectedSupport,
            $database->support(new Set($items))
        );
    }

    public function provideSupportData()
    {
        return array(
            array(
                array('A'), 1.0
            ),
            array(
                array('B'), 0.25
            ),
            array(
                array('C'), 0.5
            ),
            array(
                array('A', 'B'), 0.25
            ),
            array(
                array('A', 'C'), 0.5
            ),
            array(
                array('B', 'C'), 0.0
            ),
            array(
                array('A', 'B', 'C'), 0.0
            ),
            array(
                array('D'), 0.0
            ),
        );
    }
}
