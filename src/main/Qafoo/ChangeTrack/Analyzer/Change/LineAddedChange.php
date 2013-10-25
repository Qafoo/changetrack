<?php

namespace Qafoo\ChangeTrack\Analyzer\Change;

use Qafoo\ChangeTrack\Analyzer\Vcs\GitCheckout;

class LineAddedChange extends LineChange
{
    /**
     * Returns the absolute path of the affected file.
     *
     * @param string $beforePath
     * @param string $afterPath
     * @param \Qafoo\ChangeTrack\Analyzer\Change\FileChange $fileChange
     */
    public function determineAffectedArtifact(GitCheckout $checkout, $revision, FileChange $fileChange)
    {
        $checkout->update($revision);

        $affectedFilePath = $checkout->getLocalPath() . '/' . $fileChange->getToFile();

        return $this->reflectionLookup->getAffectedMethod($affectedFilePath, $this->affectedLine);
    }
}
