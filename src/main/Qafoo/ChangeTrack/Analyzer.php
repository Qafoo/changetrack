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
        $versions = $this->checkout->getVersions();

        $initialVersion = array_shift($versions);

        $recursiveIterator = new \RecursiveIteratorIterator(
            $this->checkout,
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $session = new ReflectionSession();
        $query = new CheckoutQuery($session->createFileQuery(), $this->checkout);

        $changes = array();

        $changeRecorder = new ChangeRecorder($initialVersion, $this->checkout->getLogEntry($initialVersion)->message);

        foreach ($recursiveIterator as $leaveNode) {
            if ($leaveNode instanceof VCSWrapper\File && substr($leaveNode->getLocalPath(), -3) == 'php') {
                foreach ($query->find($initialVersion, $leaveNode->getLocalPath()) as $class) {
                    foreach ($class->getMethods() as $method) {
                        $changeRecorder->recordChange($class, $method);
                    }
                }
            }
        }
        $changes[] = $changeRecorder;

        $previousVersion = $initialVersion;
        foreach ($versions as $currentVersion) {
            $localChanges = array();

            $changeRecorder = new ChangeRecorder($currentVersion, $this->checkout->getLogEntry($currentVersion)->message);

            $diff = new \CallbackFilterIterator(
                new DiffIterator(
                    $this->checkout->getDiff($previousVersion, $currentVersion)
                ),
                function ($diffCollection) {
                    return substr($diffCollection->to, -3) == 'php';
                }
            );
            foreach ($diff as $diffCollection) {
                $affectedFilePath = $this->checkoutPath . substr($diffCollection->to, 1);

                $classes = $query->find($currentVersion, $affectedFilePath);

                $chunksIterator = new ChunksLineFeedIterator($diffCollection->chunks);

                foreach ($chunksIterator as $lineNumber) {
                    foreach ($classes as $class) {
                        foreach ($class->getMethods() as $method) {
                            if ($lineNumber >= $method->getStartLine() && $lineNumber <= $method->getEndLine()) {
                                $changeRecorder->recordChange($class, $method);
                            }
                        }
                    }
                }
            }

            $changes[] = $changeRecorder;
            $previousVersion = $currentVersion;
        }
        return $this->mergeChanges($changes);
    }

    protected function mergeChanges(array $changeRecorders)
    {
        $mergedChanges = array();

        foreach ($changeRecorders as $changeRecorder) {
            foreach ($changeRecorder->getChanges() as $className => $methodChanges)
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
        return $mergedChanges;
    }
}
