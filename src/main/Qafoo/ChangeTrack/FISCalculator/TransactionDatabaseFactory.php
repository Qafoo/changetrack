<?php

namespace Qafoo\ChangeTrack\FISCalculator;

use Qafoo\ChangeTrack\Analyzer\Result;

abstract class TransactionDatabaseFactory
{
    /**
     * Creates a transaction database from $analysisResult
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Result $analysisResult
     * @return \Qafoo\ChangeTrack\FISCalculator\TransactionDataBase
     */
    abstract public function createDatabase(Result $analysisResult);
}
