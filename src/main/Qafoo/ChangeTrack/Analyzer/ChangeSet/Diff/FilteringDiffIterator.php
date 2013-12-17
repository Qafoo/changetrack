<?php

namespace Qafoo\ChangeTrack\Analyzer\ChangeSet\Diff;

use Qafoo\ChangeTrack\Analyzer\PathFilter;
use Qafoo\ChangeTrack\Analyzer\Change\LocalChange;

/**
 * Filters the changeset based on paths in the head revision.
 *
 * All paths are defined relative to your root folder, and are checked in this order:
 *
 * 1) A file must match at least one path defined in the paths setting. If the paths setting is empty, this will be
 *    treated like if it would contain a single path *; that is it would always match.
 * 2) A file must not match a single path defined in the excluded_paths setting.
 */
class FilteringDiffIterator implements \IteratorAggregate
{
    private $delegate;

    /**
     * @var \Qafoo\ChangeTrack\Analyzer\PathFilter
     */
    private $pathFilter;

    public function __construct(\Traversable $iterator, array $paths = array(), array $excludedPaths = array())
    {
        $this->delegate = $iterator;
        $this->pathFilter = new PathFilter($paths, $excludedPaths);
    }

    public function getIterator()
    {
        foreach ($this->delegate as $localChange) {
            /** @var LocalChange $localChange */

            if ($this->pathFilter->isFiltered($localChange->getFileChange()->getToFile())) {
                continue;
            }

            yield $localChange;
        }
    }
}
