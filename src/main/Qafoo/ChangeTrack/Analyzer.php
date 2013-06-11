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
        foreach ($recursiveIterator as $leaveNode) {
            if ($leaveNode instanceof VCSWrapper\File && substr($leaveNode->getLocalPath(), -3) == 'php') {
                foreach ($query->find($leaveNode->getLocalPath()) as $class) {
                    foreach ($class->getMethods() as $method) {
                        if (!isset($changes[$class->getName()])) {
                            $changes[$class->getName()] = array();
                        }
                        if (!isset($changes[$class->getName()][$method->getName()])) {
                            $changes[$class->getName()][$method->getName()] = 0;
                        }
                        $changes[$class->getName()][$method->getName()]++;
                    }
                }
            }
        }

        $previousVersion = $initialVersion;
        foreach ($versions as $currentVersion) {
            // $diff = $this->checkout->getDiff($currentVersion, $previousVersion);
            $diff = $this->checkout->getDiff($previousVersion, $currentVersion);
            foreach ($diff as $diffCollection) {
                $affectedFilePath = $this->checkoutPath . substr($diffCollection->to, 1);

                if (substr($affectedFilePath, -3) !== 'php') {
                    continue;
                }

                $classes = $query->find($affectedFilePath);

                echo "$affectedFilePath ($currentVersion)\n";
                foreach ($diffCollection->chunks as $chunk) {
                    $hunkStart = $chunk->end;
                    $hunkLength = $chunk->endRange;

                    $lineIndex = $hunkStart;

                    for ($lineOffset = 0; $lineOffset < $hunkLength; $lineOffset++) {
                        $line = $chunk->lines[$lineOffset];

                        switch ($line->type) {
                            case VCSWrapper\Diff\Line::ADDED:
                                $lineIndex++;
                                break;
                            case VCSWrapper\Diff\Line::REMOVED:
                                $lineIndex--;
                                break;
                            case VCSWrapper\Diff\Line::UNCHANGED:
                                $lineIndex++;
                                break;
                        }

                        foreach ($classes as $class) {
                            foreach ($class->getMethods() as $method) {
                                if ($lineIndex >= $method->getStartLine() && $lineIndex <= $method->getEndLine()) {
                                    if (!isset($changes[$class->getName()])) {
                                        $changes[$class->getName()] = array();
                                    }
                                    if (!isset($changes[$class->getName()][$method->getName()])) {
                                        $changes[$class->getName()][$method->getName()] = 0;
                                    }
                                    $changes[$class->getName()][$method->getName()]++;
                                }
                            }
                        }
                    }
                }
            }

            $previousVersion = $currentVersion;
        }
        return $changes;
    }
}
