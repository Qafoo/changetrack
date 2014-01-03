<?php

namespace Qafoo\ChangeTrack\FISCalculator;

use Qafoo\ChangeTrack\Analyzer\ResultBuilder;

class ClassTransactionDatabaseFactoryTest extends \PHPUnit_Framework_TestCase
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
        $resultBuilder->revisionChanges('2')
            ->packageChanges('A')
            ->classChanges('Bam')
            ->methodChanges('bambam')
            ->lineAdded();

        $result = $resultBuilder->buildResult();

        $databaseFactory = new ClassTransactionDatabaseFactory();
        $database = $databaseFactory->createDatabase($result);

        $this->assertEquals(
            array(
                new ClassItem('A', 'Foo'),
                new ClassItem('A', 'Bam'),
            ),
            $database->getItems()
        );
    }
}
