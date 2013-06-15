<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Change;

class ChangeRecorder
{
    private $changes = array();

    private $reflectionQuery;

    public function __construct($reflectionQuery)
    {
        $this->reflectionQuery = $reflectionQuery;
    }

    public function recordChange(Change $change)
    {
        $revision = $change->revision;

        if (!isset($this->changes[$revision])) {
            $this->changes[$revision] = array();
        }

        $affectedMethod = $this->determineAffectedMethod($change);

        if ($affectedMethod !== null) {
            $className = $affectedMethod->getDeclaringClass()->getName();
            if (!isset($this->changes[$change->revision][$className])) {
                $this->changes[$revision][$className] = array();
            }
            $this->changes[$revision][$className][$affectedMethod->getName()] = 1;
        }
    }

    /**
     * Determines which method is affected by a change
     *
     * @param Change $change
     * @return \ReflectionMethod
     */
    private function determineAffectedMethod(Change $change)
    {
        $classes = $this->reflectionQuery->find($change->localFile);
        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if ($change->affectedLine >= $method->getStartLine() && $change->affectedLine <= $method->getEndLine()) {
                    return $method;
                }
            }
        }
        return null;
    }

    public function getChanges()
    {
        return $this->changes;
    }
}
