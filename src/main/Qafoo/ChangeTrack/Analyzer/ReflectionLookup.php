<?php

namespace Qafoo\ChangeTrack\Analyzer;

use pdepend\reflection\queries\ReflectionFileQuery;

class ReflectionLookup
{
    /**
     * @var \pdepend\reflection\queries\ReflectionFileQuery
     */
    private $reflectionQuery;

    /**
     * @param \pdepend\reflection\queries\ReflectionFileQuery $reflectionQuery
     */
    public function __construct(ReflectionFileQuery $reflectionQuery)
    {
        $this->reflectionQuery = $reflectionQuery;
    }

    /**
     * @param string $file
     * @param int $line
     * @return \ReflectionMethod|null
     */
    public function getAffectedMethod($file, $line)
    {
        $classes = $this->reflectionQuery->find($file);
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
