<?php

namespace Qafoo\ChangeTrack\Analyzer\Result;

class PackageChanges
{
    /**
     * @var string
     */
    public $packageName;

    /**
     * @var Qafoo\ChangeTrack\Analyzer\Result\ClassChanges[]
     */
    public $classChanges;

    /**
     * @param string $packageName
     * @param array(ClassChanges) $classChanges
     */
    public function __construct($packageName, array $classChanges)
    {
        $this->packageName = $packageName;
        $this->classChanges = $classChanges;
    }
}
