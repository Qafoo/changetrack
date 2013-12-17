<?php

namespace Qafoo\ChangeTrack\Analyzer;

class PathFilter
{
    private $paths;
    private $excludedPaths;

    public function __construct($paths, $excludedPaths)
    {
        $this->paths = $paths;
        $this->excludedPaths = $excludedPaths;
    }

    public function isFiltered($path)
    {
        if ( ! empty($this->paths) && ! $this->matches($path, $this->paths)) {
            return true;
        }

        if ( ! empty($this->excludedPaths) && $this->matches($path, $this->excludedPaths)) {
            return true;
        }

        return false;
    }

    private function matches($path, array $patterns)
    {
        foreach ($patterns as $pattern) {
            if (fnmatch($pattern, $path)) {
                return true;
            }
        }

        return false;
    }
}
