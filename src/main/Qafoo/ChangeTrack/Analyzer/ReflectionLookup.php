<?php

namespace Qafoo\ChangeTrack\Analyzer;

use Qafoo\ChangeTrack\Analyzer\Reflection\FileQuery;

class ReflectionLookup
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\Reflection\FileQuery
     */
    private $reflectionQuery;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Reflection\FileQuery $reflectionQuery
     */
    public function __construct(FileQuery $reflectionQuery)
    {
        $this->reflectionQuery = $reflectionQuery;
    }

    /**
     * @param string $file
     * @param int $line
     * @param string $revision
     * @return \ReflectionMethod|null
     */
    public function getAffectedMethod($file, $line, $revision)
    {
        $classes = $this->reflectionQuery->find($file, $revision);
        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if ($line >= $method->getStartLine() && $line <= $method->getEndLine()) {
                    return $method;
                }
            }
        }
        return null;
    }
}
