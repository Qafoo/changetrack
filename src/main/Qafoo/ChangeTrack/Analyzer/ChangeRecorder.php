<?php

namespace Qafoo\ChangeTrack\Analyzer;

class ChangeRecorder
{
    private $revision;

    private $commitMessage;

    private $changes = array();

    public function __construct($revision, $commitMessage)
    {
        $this->revision = $revision;
        $this->commitMessage = $commitMessage;
    }

    public function recordChange(\ReflectionClass $class, \ReflectionMethod $method)
    {
        if (!isset($this->changes[$class->getName()])) {
            $this->changes[$class->getName()] = array();
        }
        $this->changes[$class->getName()][$method->getName()] = 1;
    }

    public function getChanges()
    {
        return $this->changes;
    }
}
