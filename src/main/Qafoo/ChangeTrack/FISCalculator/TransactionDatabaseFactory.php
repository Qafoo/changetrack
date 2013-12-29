<?php

namespace Qafoo\ChangeTrack\FISCalculator;

use Qafoo\ChangeTrack\Analyzer\Result;

class TransactionDatabaseFactory
{
    /**
     * Creates a transaction database from $analysisResult
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Result $analysisResult
     * @return \Qafoo\ChangeTrack\FISCalculator\TransactionDataBase
     */
    public function createDatabase(Result $analysisResult)
    {
        $transactionBase = new TransactionDatabase();

        foreach ($analysisResult->revisionChanges as $revisionChange) {
            foreach ($revisionChange->packageChanges as $packageChange) {
                foreach ($packageChange->classChanges as $classChange) {
                    foreach ($classChange->methodChanges as $methodChange) {

                        $revision = $revisionChange->revision;

                        $item = new MethodItem(
                            $packageChange->packageName,
                            $classChange->className,
                            $methodChange->methodName
                        );

                        $transactionBase->addItem($revision, $item);
                    }
                }
            }
        }

        return $transactionBase;
    }
}
