<?php

namespace Qafoo\ChangeTrack;

class TestTools
{
    /**
     * Returns $relativePath made absolute from the application base path.
     *
     * @param string $relativePath
     * @return string
     */
    public function getApplicationPath($relativePath)
    {
        return __DIR__ . '/../../../../' . $relativePath;
    }
}
