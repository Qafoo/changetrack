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
     * @param \pdepend\reflection\queries\ReflectionFileQuery $innerFileQuery
     */
    public function __construct(ReflectionFileQuery $innerFileQuery)
    {
        $this->innerFileQuery = $innerFileQuery;
    }

    /**
     * @param string $file
     * @return \ReflectionClass[]
     */
    public function find($file)
    {
        return $this->innerFileQuery->find($file);
    }
}
