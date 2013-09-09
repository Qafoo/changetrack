<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet;

use Qafoo\ChangeTrack\Analyzer\Change;
use Qafoo\ChangeTrack\Analyzer\ChangeSet;
use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;

use Arbit\VCSWrapper;

class InitialChangeSet extends ChangeSet
{
    private $checkout;

    private $revision;

    private $message;

    public function __construct(VCSWrapper\Checkout $checkout, $revision, $message)
    {
        $this->checkout = $checkout;
        $this->revision = $revision;
        $this->message = $message;
    }

    public function recordChanges(ChangeRecorder $changeRecorder)
    {
        $this->checkout->update($this->revision);

        $recursiveIterator = new \RecursiveIteratorIterator(
            $this->checkout,
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($recursiveIterator as $leaveNode) {
            // TODO: Move filtering to change recorder
            if ($leaveNode instanceof VCSWrapper\File && substr($leaveNode->getLocalPath(), -3) == 'php') {
                foreach (file($leaveNode->getLocalPath()) as $lineIndex => $lineContent) {
                    $changeRecorder->recordChange(
                        new Change(
                            $leaveNode->getLocalPath(),
                            $lineIndex + 1,
                            Change::ADDED,
                            $this->revision,
                            $this->message
                        )
                    );
                }
            }
        }
    }
}
