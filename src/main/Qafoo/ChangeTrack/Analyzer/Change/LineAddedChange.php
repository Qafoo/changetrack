<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Qafoo\ChangeTrack\Analyzer\ReflectionLookup;
use Qafoo\ChangeTrack\Analyzer\Checkout;

class LineAddedChange extends LineChange
{
    /**
     * Returns a ReflectionMethod, if a method is affected by the change
     *
     * @param \Qafoo\ChangeTrack\Analyzer\Checkout $checkout
     * @param \Qafoo\ChangeTrack\Analyzer\ReflectionLookup $reflectionLookup
     * @param string $revision
     * @param \Qafoo\ChangeTrack\Analyzer\Change\FileChange
     */
    public function determineAffectedArtifact(
        Checkout $checkout,
        ReflectionLookup $reflectionLookup,
        $revision,
        FileChange $fileChange
    ) {
        $checkout->update($revision);

        $affectedFilePath = $checkout->getLocalPath() . '/' . $fileChange->getToFile();

        return $reflectionLookup->getAffectedMethod($affectedFilePath, $this->affectedLine, $revision);
    }
}
