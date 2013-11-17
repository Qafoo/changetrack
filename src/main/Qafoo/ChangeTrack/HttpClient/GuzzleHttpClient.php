<?php

namespace Qafoo\ChangeTrack\HttpClient;

use Qafoo\ChangeTrack\HttpClient;

class GuzzleHttpClient extends HttpClient
{
    /**
     * @var \Guzzle\Http\Client
     */
    private $guzzle;

    public function __construct(\Guzzle\Http\Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Perform a GET request to $url.
     *
     * @param string $url
     * @return Qafoo\ChangeTrack\HttpClient\Response
     */
    public function get($url)
    {
        $request = $this->guzzle->get($url);
        $response = $request->send();

        return new Response(
            $response->getStatusCode(),
            $response->getBody()
        );
    }
}
