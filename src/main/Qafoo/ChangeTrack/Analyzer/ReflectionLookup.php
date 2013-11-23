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
     * @var array
     */
    private $cache;

    /**
     * @var string
     */
    private $currentRevision;

    /**
     * @param \Qafoo\ChangeTrack\Analyzer\Reflection\FileQuery $reflectionQuery
     */
    public function __construct(FileQuery $reflectionQuery)
    {
        $this->reflectionQuery = $reflectionQuery;

        $this->resetCache(null);
    }

    /**
     * @param string $file
     * @param int $line
     * @param string $revision
     * @return \ReflectionMethod|null
     */
    public function getAffectedMethod($file, $line, $revision)
    {
        if ($this->currentRevision !== $revision) {
            $this->resetCache($revision);
        }

        if (!isset($this->cache[$file])) {
            $this->cache[$file] = $this->reflectionQuery->find($file, $revision);
        }
        $classes = $this->cache[$file];

        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if ($line >= $method->getStartLine() && $line <= $method->getEndLine()) {
                    return $method;
                }
            }
        }
        return null;
    }

    /**
     * @param string $newRevision
     */
    private function resetCache($newRevision)
    {
        $this->currentRevision = $newRevision;
        $this->cache = array();
    }
}
