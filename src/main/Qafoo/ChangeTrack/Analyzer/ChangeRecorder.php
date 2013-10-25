<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Change;
use Qafoo\ChangeTrack\Analyzer\Change\LineAddedChange;
use Qafoo\ChangeTrack\Analyzer\Change\LineRemovedChange;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class ChangeRecorder
{
    private $reflectionQuery;

    private $resultBuilder;

    public function __construct($reflectionQuery, ResultBuilder $resultBuilder)
    {
        $this->reflectionQuery = $reflectionQuery;
        $this->resultBuilder = $resultBuilder;
    }

    public function recordChange(Change $change, GitCheckout $beforeCheckout, GitCheckout $afterCheckout)
    {
        /*
         * TODO: Integrate
         *
        if (substr($fileName, -3, 3) !== 'php') {
            // Skip all non-PHP files
            // @TODO: Make configurable
            continue;
        }
        */

        $afterCheckout->update($change->getRevision());

        if ($afterCheckout->hasPredecessor($change->getRevision())) {
            $beforeCheckout->update($afterCheckout->getPredecessor($change->getRevision()));
        }

        $affectedFile = $change->getAffectedFile(
            $beforeCheckout->getLocalPath(),
            $afterCheckout->getLocalPath()
        );

        $affectedMethod = $this->determineAffectedMethod($affectedFile, $change->getAffectedLine());

        if ($affectedMethod !== null) {
            $affectedClass = $affectedMethod->getDeclaringClass();

            $methodChangesBuilder = $this->resultBuilder->revisionChanges($change->getRevision())
                ->commitMessage($change->getMessage())
                ->packageChanges($affectedClass->getNamespaceName())
                ->classChanges($affectedClass->getShortName())
                ->methodChanges($affectedMethod->getName());

            switch (true) {
                case ($change->getLineChange() instanceof LineRemovedChange):
                    $methodChangesBuilder->lineRemoved();
                    break;
                case ($change->getLineChange() instanceof LineAddedChange):
                    $methodChangesBuilder->lineAdded();
                    break;
            }
        }
    }

    /**
     * Determines which method is affected by a change
     *
     * @param string $affectedFile
     * @param int $affectedLine
     * @return \ReflectionMethod
     */
    private function determineAffectedMethod($affectedFile, $affectedLine)
    {
        $classes = $this->reflectionQuery->find($affectedFile);
        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if ($affectedLine >= $method->getStartLine() && $affectedLine <= $method->getEndLine()) {
                    return $method;
                }
            }
        }
        return null;
    }
}
