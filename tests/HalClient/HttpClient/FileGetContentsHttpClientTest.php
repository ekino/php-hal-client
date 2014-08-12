<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient;

use Ekino\HalClient\HttpClient\FileGetContentsHttpClient;

class FileGetContentsHttpClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $client = new FileGetContentsHttpClient('http://example.org/', array(), 5.0);

        $response = $client->get('/');

        $this->assertInstanceOf('Ekino\HalClient\HttpClient\HttpResponse', $response);

        $this->assertEquals(200, $response->getStatus());
        $this->assertNotNull($response->getHeader('Content-Type'));

        $this->assertContains('Example Domain', $response->getBody());
    }

    public function testGet404()
    {
        $client = new FileGetContentsHttpClient('http://www.google.com/this-is-an-invalid-url', array(), 5.0);

        $response = $client->get('/');

        $this->assertEquals($response->getStatus(), 404);
    }

    /**
     * @expectedException \Ekino\HalClient\Exception\RequestException
     * @expectedExceptionMessage Empty response, no headers or impossible to reach the remote server
     */
    public function testGetUnreachable()
    {
        $client = new FileGetContentsHttpClient('https://8.8.8.8/', array(), 2.0); // Google DNS ;)

        $response = $client->get('/');

        $this->assertEquals($response->getStatus(), 404);
    }
}