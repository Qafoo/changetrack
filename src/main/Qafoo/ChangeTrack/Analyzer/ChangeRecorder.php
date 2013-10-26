<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Change;
use Qafoo\ChangeTrack\Analyzer\Change\LineAddedChange;
use Qafoo\ChangeTrack\Analyzer\Change\LineRemovedChange;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class ChangeRecorder
{
    private $resultBuilder;

    public function __construct(ResultBuilder $resultBuilder)
    {
        $this->resultBuilder = $resultBuilder;
    }

    public function recordChange(Change $change, GitCheckout $checkout)
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

        $affectedMethod = $change->determineAffectedArtifact($checkout);

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
