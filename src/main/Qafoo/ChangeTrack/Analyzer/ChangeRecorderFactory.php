<?php

namespace Qafoo\ChangeTrack\Analyzer;

class ChangeRecorderFactory
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ReflectionLookup
     */
    private $reflectionLookup;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\ReflectionLookup $reflectionLookup
     */
    public function __construct(ReflectionLookup $reflectionLookup)
    {
        $this->reflectionLookup = $reflectionLookup;
    }

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\ChangeRecorder $resultBuilder
     */
    public function createChangeRecorder(ResultBuilder $resultBuilder)
    {
        return new ChangeRecorder($resultBuilder, $this->reflectionLookup);
    }
}
