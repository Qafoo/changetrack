<?php

namespace Qafoo\ChangeTrack\Analyzer\Result;

class ClassChanges extends \ArrayObject
{
    /**
     * @var string
     */
    public $className;

    /**
     * @param string $className
     * @param \Qafoo\ChangeTrack\Analyzer\Result\MethodChanges $methodChanges
     */
    public function __construct($className, array $methodChanges)
    {
        parent::__construct($methodChanges);

        $this->className = $className;
    }
}
