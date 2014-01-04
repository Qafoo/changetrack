<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Change;
use Qafoo\ChangeTrack\Analyzer\Change\LineAddedChange;
use Qafoo\ChangeTrack\Analyzer\Change\LineRemovedChange;
use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;
use Qafoo\ChangeTrack\Analyzer\Reflection\ReflectionException;

use Qafoo\ChangeTrack\Analyzer\Checkout;

class ChangeRecorder
{
    private $resultBuilder;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ReflectionLookup $reflectionLookup
     */
    private $reflectionLookup;

    public function __construct(
        ResultBuilder $resultBuilder,
        ReflectionLookup $reflectionLookup
    ) {
        $this->resultBuilder = $resultBuilder;
        $this->reflectionLookup = $reflectionLookup;
    }

    public function recordChange(Change $change, Checkout $checkout)
    {
        $affectedMethod = null;
        try {
            $affectedMethod = $change->determineAffectedArtifact(
                $checkout,
                $this->reflectionLookup
            );
        } catch (ReflectionException $e) {
            // TODO: Implement sensible logging
            return null;
        }

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
}
