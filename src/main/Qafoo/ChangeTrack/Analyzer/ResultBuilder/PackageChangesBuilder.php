<?php

namespace Qafoo\ChangeTrack\Analyzer\ResultBuilder;

use Qafoo\ChangeTrack\Analyzer\Result\PackageChanges;

class PackageChangesBuilder
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ResultBuilder\ClassChangesBuilder[]
     */
    private $classChangesBuilders = array();

    /**
     * @param string $packageName
     */
    public function __construct($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * @param string $className
     * @return \Qafoo\ChangeTrack\Analyzer\ResultBuilder\ClassChangesBuilder
     */
    public function classChanges($className)
    {
        if (!isset($this->classChangesBuilders[$className])) {
            $this->classChangesBuilders[$className] = new ClassChangesBuilder(
                $className
            );
        }
        return $this->classChangesBuilders[$className];
    }

    /**
     * @return \Qafoo\ChangeTrack\Analyzer\Result\PackageChanges
     */
    public function buildPackageChanges()
    {
        return new PackageChanges(
            $this->packageName,
            array_map(
                function ($classChangesBuilder) {
                    return $classChangesBuilder->buildClassChanges();
                },
                $this->classChangesBuilders
            )
        );
    }
}
