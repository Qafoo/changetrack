<?php

namespace Qafoo\ChangeTrack\Analyzer\Reflection;

use pdepend\reflection\queries\ReflectionFileQuery;
use pdepend\reflection\exceptions\ParserException;

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
        try {
            $result = $this->innerFileQuery->find($file);
        } catch (ParserException $e) {
            throw new ReflectionException($file, $e);
        }
        return $result;
    }
}
