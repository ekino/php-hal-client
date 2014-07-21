<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Exporter\Test;

use Ekino\HalClient\HttpClient\HttpResponse;
use Ekino\HalClient\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testHandler()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');

        $resource = new Resource($client);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage HttpClient does not return a status code, given: 500
     */
    public function testInvalidStatus()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');
        $client->expects($this->once())->method('get')->will($this->returnValue(new HttpResponse(500)));

        $resource = new Resource($client, array(), array(
            'foo' => array(
                'href' => 'http://fake.com/foo',
                'title' => 'foo'
            )
        ));

        $resource->get('foo');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid resource, not `self` reference available
     */
    public function testRefreshWithInvalidSelfReference()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');
        $client->expects($this->never())->method('get');

        $resource = new Resource($client);

        $resource->refresh();
    }

    public function testRefresh()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');
        $client->expects($this->exactly(1))->method('get')->will($this->returnCallback(function($url) {
            if ($url == 'http://propilex.herokuapp.com') {
                return new HttpResponse(200, array(
                        'Content-Type' => 'application/hal+json'
                ), file_get_contents(__DIR__.'/../fixtures/entry_point.json'));
            }
        }));

        $resource = new Resource($client, array(), array(
            'self' => array('href'=>'http://propilex.herokuapp.com')
        ));

        $this->assertNull($resource->get('field'));
        $resource->refresh();

        $this->assertEquals($resource->get('field'), 'value');
    }
}