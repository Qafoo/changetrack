<?php

namespace Qafoo\ChangeTrack\Analyzer\Reflection;

use pdepend\reflection\queries\ReflectionFileQuery;

class FileQuery
{
    /**
     * @var \pdepend\reflection\queries\ReflectionFileQuery
     */
    private $innerFileQuery;

    /**
     * @var string
     */
    private $currentRevision;

    /**
     * @var array(string => \ArrayIterator)
     */
    private $cache = array();

    /**
     * @param \pdepend\reflection\queries\ReflectionFileQuery $innerFileQuery
     */
    public function __construct(ReflectionFileQuery $innerFileQuery)
    {
        $this->innerFileQuery = $innerFileQuery;
    }

    /**
     * @param string $file
     * @param string $revision
     */
    public function find($file, $revision)
    {
        $this->resetIfNecessary($revision);

        if (!isset($this->cache[$file])) {
            $this->cache[$file] = $this->innerFileQuery->find($file);
        }
        return $this->cache[$file];
    }

    /**
     * @param string $revision
     */
    private function resetIfNecessary($revision)
    {
        if ($this->currentRevision !== $revision) {
            $this->cache = array();
            $this->currentRevision = $revision;
        }
    }
}
