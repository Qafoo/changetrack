<?php

namespace Qafoo\ChangeTrack\HttpClient;

class Response
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $body;

    public function __construct($status, $body = '')
    {
        $this->status = $status;
        $this->body = $body;
    }
}
