<?php

namespace Qafoo\ChangeTrack\Analyzer\Result;

class MethodChanges
{
    /**
     * @var string
     */
    public $methodName;

    /**
     * @var string
     */
    public $numLinesAdded;

    /**
     * @var string
     */
    public $numLinesRemoved;

    /**
     * @param string $methodName
     * @param int $numLinesAdded
     * @param int $numLinesRemoved
     */
    public function __construct($methodName, $numLinesAdded, $numLinesRemoved)
    {
        $this->methodName = $methodName;
        $this->numLinesAdded = $numLinesAdded;
        $this->numLinesRemoved = $numLinesRemoved;
    }
}
