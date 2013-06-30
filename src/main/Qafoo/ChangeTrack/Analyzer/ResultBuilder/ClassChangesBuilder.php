<?php

namespace Qafoo\ChangeTrack\Analyzer\ResultBuilder;

use Qafoo\ChangeTrack\Analyzer\Result\ClassChanges;

class ClassChangesBuilder
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ResultBuilder\MethodChangesBuilder[]
     */
    private $methodChangesBuilders = array();

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param string $methodName
     * @return \Qafoo\ChangeTrack\Analyzer\ResultBuilder\MethodChangesBuilder
     */
    public function methodChanges($methodName)
    {
        if (!isset($this->methodChangesBuilders[$methodName])) {
            $this->methodChangesBuilders[$methodName] = new MethodChangesBuilder(
                $methodName
            );
        }
        return $this->methodChangesBuilders[$methodName];
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result\ClassChanges
     */
    public function buildClassChanges()
    {
        return new ClassChanges(
            $this->className,
            array_map(
                function ($methodChangesBuilder) {
                    return $methodChangesBuilder->buildMethodChanges();
                },
                $this->methodChangesBuilders
            )
        );
    }
}
