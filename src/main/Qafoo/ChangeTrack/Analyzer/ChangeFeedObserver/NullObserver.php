<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeFeedObserver;

use Qafoo\ChangeTrack\Analyzer\ChangeFeedObserver;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;

class NullObserver implements ChangeFeedObserver
{
    /**
     * @param array(string) $revisions
     */
    public function notifyInitialized(array $revisions)
    {
        // NOOP
    }

    /**
     * @param int $number
     * @param string $revision
     */
    public function notifyProcessingRevision($number, $revision)
    {
        // NOOP
    }
}
