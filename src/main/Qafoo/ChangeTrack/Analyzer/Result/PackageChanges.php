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
    public function __construct($packageName, array $classChanges = array())
    {
        parent::__construct($classChanges);

        $this->packageName = $packageName;
    }

    public function createClassChanges($className)
    {
        if (!isset($this[$className])) {
            $this[$className] = new ClassChanges($className);
        }
        return $this[$className];
    }
}
