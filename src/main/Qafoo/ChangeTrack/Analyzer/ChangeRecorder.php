<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Change;

class ChangeRecorder
{
    private $reflectionQuery;

    public function __construct($reflectionQuery, Result $result)
    {
        $this->reflectionQuery = $reflectionQuery;
        $this->result = $result;
    }

    public function recordChange(Change $change)
    {
        $revisionChange = $this->result->createRevisionChanges(
            $change->revision,
            $change->message
        );

        $affectedMethod = $this->determineAffectedMethod($change);

        if ($affectedMethod !== null) {

            $affectedClass = $affectedMethod->getDeclaringClass();

            $packageName = $affectedClass->getNamespaceName();
            $className = $affectedClass->getShortName();
            $methodName = $affectedMethod->getName();

            $packageChanges = $revisionChange->createPackageChanges($packageName);
            $classChange = $packageChanges->createClassChanges($className);
            $methodChange = $classChange->createMethodChanges($methodName);

            switch ($change->changeType) {
                case Change::REMOVED:
                    $methodChange->numLinesRemoved++;
                    break;
                case Change::ADDED:
                    $methodChange->numLinesAdded++;
                    break;
            }
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
