<?php

namespace Qafoo\ChangeTrack;

use pdepend\reflection\ReflectionSession;
use Arbit\VCSWrapper;

class Analyzer
{
    private $checkout;

    private $checkoutPath;

    public function __construct($repositoryUrl)
    {
        VCSWrapper\Cache\Manager::initialize(__DIR__ . '/../../../var/tmp/cache');

        $this->checkoutPath = __DIR__ . '/../../../var/tmp/checkout';

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
        $query = $session->createFileQuery();

        $changes = array();

        $this->checkout->update($initialVersion);

        $localChanges = array();
        foreach ($recursiveIterator as $leaveNode) {
            if ($leaveNode instanceof VCSWrapper\File && substr($leaveNode->getLocalPath(), -3) == 'php') {
                foreach ($query->find($leaveNode->getLocalPath()) as $class) {
                    foreach ($class->getMethods() as $method) {
                        $localChanges = $this->recordChange($localChanges, $class, $method);
                    }
                }
            }
        }

        $changes = $this->mergeChanges($changes, $localChanges);

        $previousVersion = $initialVersion;
        foreach ($versions as $currentVersion) {
            $localChanges = array();

            $this->checkout->update($currentVersion);

            $diff = $this->checkout->getDiff($previousVersion, $currentVersion);
            foreach ($diff as $diffCollection) {
                $affectedFilePath = $this->checkoutPath . substr($diffCollection->to, 1);

                if (substr($affectedFilePath, -3) !== 'php') {
                    continue;
                }

                $classes = $query->find($affectedFilePath);

                foreach ($diffCollection->chunks as $chunk) {
                    $hunkStart = $chunk->end;
                    $hunkLength = $chunk->endRange;

                    $lineIndex = $hunkStart;

                    for ($lineOffset = 0; $lineOffset < $hunkLength; $lineOffset++) {
                        $line = $chunk->lines[$lineOffset];

                        switch ($line->type) {
                            case VCSWrapper\Diff\Line::ADDED:
                            case VCSWrapper\Diff\Line::UNCHANGED:
                                $lineIndex++;
                                break;
                            case VCSWrapper\Diff\Line::REMOVED:
                                // No forward
                                break;
                        }

                        foreach ($classes as $class) {
                            foreach ($class->getMethods() as $method) {
                                if ($lineIndex >= $method->getStartLine() && $lineIndex <= $method->getEndLine()) {
                                    $localChanges = $this->recordChange($localChanges, $class, $method);
                                }
                            }
                        }
                    }
                }
            }

            $changes = $this->mergeChanges($changes, $localChanges);
            $previousVersion = $currentVersion;
        }
        return $changes;
    }

    protected function mergeChanges($previousChanges, $currentChanges)
    {
        foreach ($currentChanges as $className => $methodChanges) {
            foreach ($methodChanges as $methodName => $changeCount) {
                if (!isset($previousChanges[$className])) {
                    $previousChanges[$className] = array();
                }
                if (!isset($previousChanges[$className][$methodName])) {
                    $previousChanges[$className][$methodName] = 0;
                }
                $previousChanges[$className][$methodName] += $changeCount;
            }
        }
        return $previousChanges;
    }

    protected function recordChange($changes, $class, $method)
    {
        if (!isset($changes[$class->getName()])) {
            $changes[$class->getName()] = array();
        }
        $changes[$class->getName()][$method->getName()] = 1;

        return $changes;
    }
}
