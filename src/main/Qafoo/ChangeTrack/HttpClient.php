<?php

namespace Qafoo\ChangeTrack;

abstract class HttpClient
{
    /**
     * Perform a GET request to $url.
     *
     * @param string $url
     * @return Qafoo\ChangeTrack\HttpClient\Response
     */
    abstract public function get($url);
}
