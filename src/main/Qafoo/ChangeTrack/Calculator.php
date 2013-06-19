<?php

namespace Qafoo\ChangeTrack;

use Qafoo\ChangeTrack\Analyzer\Result;

class Calculator
{
    private $analysisResult;

    public function __construct(Result $analysisResult)
    {
        $this->analysisResult = $analysisResult;
    }

    public function calculateStats()
    {
        $stats = array();
        foreach ($this->analysisResult as $revisionChanges) {
            $changeType = 'misc';
            switch (true) {
                case (preg_match('(fix)i', $revisionChanges->commitMessage) > 0):
                    $changeType = 'fix';
                    break;
                case (preg_match('(implemented)i', $revisionChanges->commitMessage) > 0):
                    $changeType = 'implement';
                    break;
            }

            foreach ($revisionChanges as $classChanges) {
                foreach ($classChanges as $methodChanges) {
                    if (!isset($stats[$changeType])) {
                        $stats[$changeType] = array();
                    }
                    if (!isset($stats[$changeType][$classChanges->className])) {
                        $stats[$changeType][$classChanges->className] = array();
                    }
                    if (!isset($stats[$changeType][$classChanges->className][$methodChanges->methodName])) {
                        $stats[$changeType][$classChanges->className][$methodChanges->methodName] = 0;
                    }
                    $stats[$changeType][$classChanges->className][$methodChanges->methodName]++;
                }
            }
        }
        return $stats;
    }
}
