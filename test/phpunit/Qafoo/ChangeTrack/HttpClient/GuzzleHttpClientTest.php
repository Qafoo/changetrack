<?php

namespace Qafoo\ChangeTrack\HttpClient;

/**
 * @group integration
 */
class GuzzleHttpClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRequest()
    {
        $httpClient = new GuzzleHttpClient(new \Guzzle\Http\Client());

        $response = $httpClient->get('http://google.com');

        $this->assertEquals(200, $response->status);
        $this->assertNotNull($response->body);
    }
}
