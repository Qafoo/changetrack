<?php

namespace Qafoo\ChangeTrack\Analyzer;

class PathFilter
{
    /**
     * @see fnmatch()
     * @var string[]
     */
    private $paths;

    /**
     * @see fnmatch()
     * @var string[]
     */
    private $excludedPaths;

    /**
     * @param string[] $paths
     * @param string[] $excludedPaths
     */
    public function __construct($paths, $excludedPaths)
    {
        $this->paths = $paths;
        $this->excludedPaths = $excludedPaths;
    }

    /**
     * Returns true if $path should not be analyzed
     *
     * @param string $path
     * @return bool
     */
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

    /**
     * Returns true if $path matches at least on of $patterns
     *
     * @param string $path
     * @param string[] $patterns {@link fnmatch()}
     */
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
