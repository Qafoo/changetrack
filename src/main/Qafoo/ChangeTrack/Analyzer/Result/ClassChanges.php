<?php

namespace Qafoo\ChangeTrack\Analyzer\Result;

class ClassChanges
{
    /**
     * @var string
     */
    public $className;

    /**
     * @var Qafoo\ChangeTrack\Analyzer\Result\MethodChanges[]
     */
    public $methodChanges;

    /**
     * @param string $className
     * @param \Qafoo\ChangeTrack\Analyzer\Result\MethodChanges $methodChanges
     */
    public function __construct($className, array $methodChanges)
    {
        $this->className = $className;
        $this->methodChanges = $methodChanges;
    }
}
