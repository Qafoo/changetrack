<?php

namespace Qafoo\ChangeTrack\Analyzer\Reflection;

use pdepend\reflection\interfaces\SourceResolver;
use pdepend\reflection\exceptions\PathnameNotFoundException;

/**
 * SourceResolver that does not know about any source files.
 *
 * Since we only use file queries to determine the actual components of a
 * single class, we don't need any resolving of related classes.
 */
class NullSourceResolver implements SourceResolver
{
    /**
     * @param string $className
     * @return boolean
     */
    public function hasPathnameForClass($className)
    {
        return false;
    }

    /**
     * @param string $className
     * @return string
     * @throws \pdepend\reflection\exceptions\PathnameNotFoundException When
     *         not match can be found for the given class name.
     */
    public function getPathnameForClass($className)
    {
        throw new PathnameNotFoundException($className);
    }
}
