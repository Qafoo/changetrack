<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class TransactionDatabaseTest extends \PHPUnit_Framework_TestCase
{
    public function testItemsRegisteredDuringConstruction()
    {
        $database = new TransactionDatabase(
            array(
                '1' => array(new StringItem('A'), new StringItem('C')),
                '2' => array(new StringItem('B'), new StringItem('C')),
            )
        );

        $this->assertEquals(
            array(new StringItem('A'), new StringItem('C'), new StringItem('B')),
            $database->getItems()
        );
    }

    public function testItemsRegisteredOnAdd()
    {
        $database = new TransactionDatabase();

        $database->addItem('1', new StringItem('A'));
        $database->addItem('1', new StringItem('C'));
        $database->addItem('2', new StringItem('B'));
        $database->addItem('2', new StringItem('C'));

        $this->assertEquals(
            array(new StringItem('A'), new StringItem('C'), new StringItem('B')),
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
        $database = new TransactionDatabase(
            array(
                '1' => array(new StringItem('A'), new StringItem('C')),
                '2' => array(new StringItem('A')),
                '3' => array(new StringItem('A'), new StringItem('B')),
                '4' => array(new StringItem('A'), new StringItem('C'))
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
                array(new StringItem('A')), 1.0
            ),
            array(
                array(new StringItem('B')), 0.25
            ),
            array(
                array(new StringItem('C')), 0.5
            ),
            array(
                array(new StringItem('A'), new StringItem('B')), 0.25
            ),
            array(
                array(new StringItem('A'), new StringItem('C')), 0.5
            ),
            array(
                array(new StringItem('B'), new StringItem('C')), 0.0
            ),
            array(
                array(new StringItem('A'), new StringItem('B'), new StringItem('C')), 0.0
            ),
            array(
                array(new StringItem('D')), 0.0
            ),
        );
    }
}
