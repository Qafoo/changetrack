<?php

namespace Qafoo\ChangeTrack\FISCalculator;

class TransactionDatabaseFactoryLocator
{
    /**
     * @var \Qafoo\ChangeTrack\FISCalculator\TransactionDatabaseFactory[string]
     */
    private $typeMap;

    /**
     * @param \Qafoo\ChangeTrack\FISCalculator\TransactionDatabaseFactory[string] $typeMap
     */
    public function __construct(array $typeMap)
    {
        $this->typeMap = $typeMap;
    }

    /**
     * @param string $type
     * @return \Qafoo\ChangeTrack\FISCalculator\TransactionDatabaseFactory
     */
    public function getFactoryByType($type)
    {
        if (!isset($this->typeMap[$type])) {
            throw new \RuntimeException(
                sprintf('No transaction database factory found for "%s"', $type)
            );
        }
        return $this->typeMap[$type];
    }
}
