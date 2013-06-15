<?php

namespace Qafoo\ChangeTrack;

use pdepend\reflection\ReflectionSession;
use Arbit\VCSWrapper;

use Qafoo\ChangeTrack\Analyzer\ChangeRecorder;
use Qafoo\ChangeTrack\Analyzer\DiffIterator;
use Qafoo\ChangeTrack\Analyzer\LineFeed\ChunksLineFeedIterator;

class Analyzer
{
    private $checkout;

    private $checkoutPath;

    public function __construct($repositoryUrl, $checkoutPath, $cachePath)
    {
        VCSWrapper\Cache\Manager::initialize($cachePath);

        $this->checkoutPath = $checkoutPath;

        $this->checkout = new VCSWrapper\GitCli\Checkout($this->checkoutPath);
        $this->checkout->initialize($repositoryUrl);
    }

    public function analyze()
    {
        $session = new ReflectionSession();
        $query = $session->createFileQuery();

        $changeFeed = new ChangeFeed($this->checkout);
        $changeRecorder = new ChangeRecorder($query);

        foreach ($changeFeed as $changeSet) {
            $changeSet->recordChanges($changeRecorder);
        }
        return $this->mergeChanges($changeRecorder->getChanges());
    }

    protected function mergeChanges(array $changes)
    {
        $mergedChanges = array();

        foreach ($changes as $revision => $revisionChanges) {
            foreach ($revisionChanges as $className => $methodChanges) {
                foreach ($methodChanges as $methodName => $changeCount) {
                    if (!isset($mergedChanges[$className])) {
                        $mergedChanges[$className] = array();
                    }
                    if (!isset($mergedChanges[$className][$methodName])) {
                        $mergedChanges[$className][$methodName] = 0;
                    }
                    $mergedChanges[$className][$methodName] += $changeCount;
                }
            }
        }
        return $mergedChanges;
    }
}
