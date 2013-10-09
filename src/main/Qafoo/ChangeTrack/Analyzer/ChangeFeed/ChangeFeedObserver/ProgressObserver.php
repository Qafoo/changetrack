<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver;

use Qafoo\ChangeTrack\Analyzer\ChangeFeed\ChangeFeedObserver;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressObserver implements ChangeFeedObserver
{
    /**
     * @var \Symfony\Component\Console\Helper\ProgressHelper
     */
    private $progress;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var String[]
     */
    private $revisions = array();

    /**
     * @param \Symfony\Component\Console\Helper\ProgressHelper $progress
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(ProgressHelper $progress, OutputInterface $output)
    {
        $this->progress = $progress;
        $this->output = $output;
    }

    /**
     * @param array(string) $revisions
     */
    public function notifyInitialized(array $revisions)
    {
        $this->revisions = $revisions;
        $this->progress->start($this->output, count($revisions));
    }

    /**
     * @param int $number
     * @param string $revision
     */
    public function notifyProcessingRevision($number, $revision)
    {
        $this->progress->advance();

        if ($number == count($this->revisions) - 1) {
            $this->progress->finish();
        }
    }
}
