<?php

namespace Qafoo\ChangeTrack\Analyzer\ResultBuilder;

use Qafoo\ChangeTrack\Analyzer\Result\MethodChanges;

class MethodChangesBuilder
{
    /**
     * @var string
     */
    private $methodName;

    /**
     * @var int
     */
    private $numLinesAdded = 0;

    /**
     * @var int
     */
    private $numLinesRemoved = 0;

    /**
     * @param string $methodName
     */
    public function __construct($methodName)
    {
        $this->methodName = $methodName;
    }

    public function lineAdded()
    {
        $this->numLinesAdded++;
        return $this;
    }

    public function lineRemoved()
    {
        $this->numLinesRemoved++;
        return $this;
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result\MethodChanges
     */
    public function buildMethodChanges()
    {
        return new MethodChanges(
            $this->methodName,
            $this->numLinesAdded,
            $this->numLinesRemoved
        );
    }
}
