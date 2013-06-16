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
            $methodName = $affectedMethod->getName();

            if (!isset($this->changes[$change->revision][$className])) {
                $this->changes[$revision][$className] = array();
            }
            if (!isset($this->changes[$change->revision][$className][$methodName])) {
                $this->changes[$revision][$className][$methodName] = array(
                    Change::ADDED => 0,
                    Change::REMOVED => 0
                );
            }
            $this->changes[$revision][$className][$methodName][$change->changeType]++;
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
