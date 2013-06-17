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
     * @param array(MethodChanges) $methodChanges
     */
    public function __construct($className, array $methodChanges = array())
    {
        parent::__construct($methodChanges);

        $this->className = $className;
    }

    public function createMethodChanges($methodName)
    {
        if (!isset($this[$methodName])) {
            $this[$methodName] = new MethodChanges($methodName);
        }
        return $this[$methodName];
    }
}
