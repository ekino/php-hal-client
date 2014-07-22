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
        $client = new FileGetContentsHttpClient();
        $client->setBaseUrl('http://www.ekino.com');

        $response = $client->get('/');

        $this->assertInstanceOf('Ekino\HalClient\HttpClient\HttpResponse', $response);

        $this->assertEquals(200, $response->getStatus());
        $this->assertNotNull($response->getHeader('Content-Type'));

        $this->assertContains('ekino', $response->getBody());
    }
}