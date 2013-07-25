<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Change;

class ChangeRecorder
{
    private $reflectionQuery;

    private $resultBuilder;

    public function __construct($reflectionQuery, ResultBuilder $resultBuilder)
    {
        $this->reflectionQuery = $reflectionQuery;
        $this->resultBuilder = $resultBuilder;
    }

    public function recordChange(Change $change)
    {
        $affectedMethod = $this->determineAffectedMethod($change);

        if ($affectedMethod !== null) {
            $affectedClass = $affectedMethod->getDeclaringClass();

            $methodChangesBuilder = $this->resultBuilder->revisionChanges($change->revision)
                ->commitMessage($change->message)
                ->packageChanges($affectedClass->getNamespaceName())
                ->classChanges($affectedClass->getShortName())
                ->methodChanges($affectedMethod->getName());

            switch ($change->changeType) {
                case Change::REMOVED:
                    $methodChangesBuilder->lineRemoved();
                    break;
                case Change::ADDED:
                    $methodChangesBuilder->lineAdded();
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
            try {
                foreach ($class->getMethods() as $method) {
                    if ($change->affectedLine >= $method->getStartLine() && $change->affectedLine <= $method->getEndLine()) {
                        return $method;
                    }
                }
            } catch (\ReflectionException $e) {
                // Thrown for classes, which are frome external projects
                continue;
            }
        }
        return null;
    }
}
