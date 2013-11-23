<?php

namespace Qafoo\ChangeTrack\Analyzer;

use pdepend\reflection\ReflectionSession;
use Qafoo\ChangeTrack\Analyzer\Reflection\FileQuery;
use Qafoo\ChangeTrack\Analyzer\Reflection\NullSourceResolver;

class ReflectionLookupFactory
{
    /**
     * @var \Qafoo\ChangeTrack\Analyzer\ReflectionLookup
     */
    private $reflectionLookup;

    public function createReflectionLookup()
    {
        if (!isset($this->reflectionLookup)) {
            $sourceResolver = new NullSourceResolver();
            $session = ReflectionSession::createDefaultSession($sourceResolver);
            $query = new FileQuery($session->createFileQuery());

            $this->reflectionLookup = new ReflectionLookup($query);
        }
        return $this->reflectionLookup;
    }
}
