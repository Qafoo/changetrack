<?php

namespace Qafoo\ChangeTrack\Analyzer\Result;

class PackageChanges extends \ArrayObject
{
    /**
     * @var string
     */
    public $packageName;

    /**
     * @param string $packageName
     * @param array(ClassChanges) $classChanges
     */
    public function __construct($packageName, array $classChanges)
    {
        parent::__construct($classChanges);

        $this->packageName = $packageName;
    }
}
