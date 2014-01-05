<?php

namespace Qafoo\ChangeTrack\Analyzer\Diff;

class Line
{
    const ADDED = 1;
    const REMOVED = 2;
    const UNCHANGED = 3;

    /**
     * @var const ADDED|REMOVED|UNCHANGED
     */
    public $type = null;

    /**
     * @var string
     */
    public $content = null;

    /**
     * @param const $type
     * @param string $content
     */
    public function __construct($type, $content)
    {
        $this->type = $type;
        $this->content = $content;
    }
}
