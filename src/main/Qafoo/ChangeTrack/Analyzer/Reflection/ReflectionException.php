<?php

namespace Qafoo\ChangeTrack\Analyzer\Reflection;

class ReflectionException extends \Exception
{
    /**
     * @param string $affectedFile
     * @param \Exception $source
     */
    public function __construct($affectedFile, \Exception $source = null)
    {
        parent::__construct(
            sprintf('Could not reflect file "%s"', $affectedFile),
            0,
            $source
        );
    }
}
