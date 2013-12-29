<?php

namespace Qafoo\ChangeTrack\FISCalculator;

use Qafoo\ChangeTrack\Analyzer\ResultBuilder;

class TransactionDatabaseFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDatabase()
    {
        $resultBuilder = new ResultBuilder('irrelevant-repository');

        $resultBuilder->revisionChanges('1')
            ->packageChanges('A')
            ->classChanges('Foo')
            ->methodChanges('bar')
            ->lineAdded();
        $resultBuilder->revisionChanges('2')
            ->packageChanges('A')
            ->classChanges('Foo')
            ->methodChanges('baz')
            ->lineAdded();

        $result = $resultBuilder->buildResult();

        $databaseFactory = new TransactionDatabaseFactory();
        $database = $databaseFactory->createDatabase($result);

        $this->assertEquals(
            array(
                new MethodItem('A', 'Foo', 'bar'),
                new MethodItem('A', 'Foo', 'baz'),
            ),
            $database->getItems()
        );
    }
}
