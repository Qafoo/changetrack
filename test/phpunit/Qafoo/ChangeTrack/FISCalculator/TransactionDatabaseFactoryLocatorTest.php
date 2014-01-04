<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class TransactionDatabaseFactoryLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testMapTypes()
    {
        $factoryA = $this->getMock('Qafoo\\ChangeTrack\\FISCalculator\\TransactionDatabaseFactory');
        $factoryB = $this->getMock('Qafoo\\ChangeTrack\\FISCalculator\\TransactionDatabaseFactory');

        $locator = new TransactionDatabaseFactoryLocator(
            array(
                'A' => $factoryA,
                'B' => $factoryB,
            )
        );

        $this->assertSame($factoryB, $locator->getFactoryByType('B'));
    }

    public function testMapFailure()
    {
        $locator = new TransactionDatabaseFactoryLocator(array());

        $this->setExpectedException('\\RuntimeException');

        $locator->getFactoryByType('A');
    }
}
