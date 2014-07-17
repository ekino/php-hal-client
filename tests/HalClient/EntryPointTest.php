<?php

namespace Ekino\HalClient;

use Ekino\HalClient\EntryPoint;
use Ekino\HalClient\HttpClient\HttpResponse;

class EntryPointTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidContentType()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');
        $client->expects($this->once())->method('get')->will($this->returnValue(new HttpResponse(array(
            'Content-Type' => 'application/json'
        )), '{}'));

        $entryPoint = new EntryPoint('http://propilex.herokuapp.com', $client);

        $entryPoint->get();
    }

    public function testGetResource()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');
        $client->expects($this->any())->method('get')->will($this->returnCallback(function($url) {

            if ($url == 'http://propilex.herokuapp.com') {
                return new HttpResponse(array(
                        'Content-Type' => 'application/hal+json'
                ), file_get_contents(__DIR__.'/../fixtures/entry_point.json'));
            }

            if ($url == 'http://propilex.herokuapp.com/documents') {
                return new HttpResponse(array(
                        'Content-Type' => 'application/hal+json'
                ), file_get_contents(__DIR__.'/../fixtures/documents.json'));

            }
        }));

        $entryPoint = new EntryPoint('http://propilex.herokuapp.com', $client);

        $resource = $entryPoint->get();

        $this->assertInstanceOf('Ekino\HalClient\Resource', $resource);
        $this->assertEmpty($resource->getProperties());
        $this->assertEmpty($resource->getEmbedded());

        $link = $resource->getLink('p:documents');

        $this->assertInstanceOf('Ekino\HalClient\Link', $link);

        $this->assertEquals($link->getHref(), 'http://propilex.herokuapp.com/documents');

        $this->assertNull($resource->get('fake'));

        $resource = $resource->get('p:documents');

        $this->assertInstanceOf('Ekino\HalClient\Resource', $resource);

        $expected = array(
            "page" => 1,
            "limit" => 10,
            "pages" => 1,
        );

        $this->assertEquals($expected, $resource->getProperties());
        $this->assertEquals(1, $resource->get('page'));
        $this->assertEquals(10, $resource->get('limit'));
        $this->assertEquals(1, $resource->get('pages'));

        $this->assertInstanceOf('Ekino\HalClient\ResourceCollection', $resource->get('documents'));
    }
}
