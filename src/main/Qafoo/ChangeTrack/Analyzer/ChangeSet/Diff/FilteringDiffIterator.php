<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\Change\LocalChange;

/**
 * Filters the changeset based on paths in the head revision.
 *
 * All paths are defined relative to your root folder, and are checked in this order:
 *
 * 1) A file must match at least one path defined in the paths setting. If the paths setting is empty, this will be
 *    treated like if it would contain a single path *; that is it would always match.
 * 2) A file must not match a single path defined in the excluded_paths setting.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FilteringDiffIterator implements \IteratorAggregate
{
    private $delegate;
    private $paths;
    private $excludedPaths;

    public function __construct(\Traversable $iterator, array $paths = array(), array $excludedPaths = array())
    {
        $this->delegate = $iterator;
        $this->paths = $paths;
        $this->excludedPaths = $excludedPaths;
    }

    public function getIterator()
    {
        foreach ($this->delegate as $localChange) {
            /** @var LocalChange $localChange */

            if ($this->isFiltered($localChange->getFileChange()->getToFile())) {
                continue;
            }

            yield $localChange;
        }
    }

    private function isFiltered($path)
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