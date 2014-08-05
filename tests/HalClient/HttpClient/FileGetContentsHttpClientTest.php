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
        $client = new FileGetContentsHttpClient('http://www.ekino.com', array(), 5.0);

        $response = $client->get('/');

        $this->assertInstanceOf('Ekino\HalClient\HttpClient\HttpResponse', $response);

        $this->assertEquals(200, $response->getStatus());
        $this->assertNotNull($response->getHeader('Content-Type'));

        $this->assertContains('ekino', $response->getBody());
    }

    /**
     * @expectedException \Ekino\HalClient\HttpClient\RequestFailedException
     * @expectedExceptionMessage Couldn't reach `http://www.ekino.com/invalidUri/`; maybe something is wrong with the host?
     */
    public function testGetUnreachable()
    {
        $client = new FileGetContentsHttpClient('http://www.ekino.com/invalidUri', array(), 5.0);

        $client->get('/');
    }
}