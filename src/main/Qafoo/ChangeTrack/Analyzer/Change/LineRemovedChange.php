<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class LineRemovedChange extends LineChange
{
    /**
     * Returns a ReflectionMethod, if a method is affected by the change
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout $checkout
     * @param string $revision
     * @param \Qafoo\ChangeTrack\Analyzer\Change\FileChange
     */
    public function determineAffectedArtifact(GitCheckout $checkout, $revision, FileChange $fileChange)
    {
        $previousRevision = $checkout->getPredecessor($revision);
        $checkout->update($previousRevision);

        $affectedFilePath = $checkout->getLocalPath() . '/' . $fileChange->getFromFile();

        return $this->reflectionLookup->getAffectedMethod($affectedFilePath, $this->affectedLine, $previousRevision);
    }
}
