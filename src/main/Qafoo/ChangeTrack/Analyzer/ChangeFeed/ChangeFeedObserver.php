<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeFeed;

interface ChangeFeedObserver
{
    /**
     * The ChangeFeed was initialized with $revisions
     *
     * @param array(string) $revisions
     */
    public function notifyInitialized(array $revisions);

    /**
     * The ChangeFeed is no processing $revision number $number.
     *
     * @param int $number
     * @param string $revision
     */
    public function notifyProcessingRevision($number, $revision);
}
