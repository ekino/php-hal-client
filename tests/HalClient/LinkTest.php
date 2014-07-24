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

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Ekino\HalClient\HttpClient\HttpClientInterface
     */
    protected $client;

    /**
     * @var \Ekino\HalClient\Resource
     */
    protected $resource;

    public function testGetCurieName()
    {
        $this->assertInstanceOf('Ekino\\HalClient\\Link', $this->resource->getLink('test:media'));

        $this->assertEquals('test', $this->resource->getLink('test:media')->getNCName());
        $this->assertEquals('media', $this->resource->getLink('test:media')->getReference());
        $this->assertEquals('test:media', $this->resource->getLink('test:media')->getName());

        $this->assertInstanceOf('Ekino\\HalClient\\Link', $this->resource->getLink('article'));

        $this->assertNull($this->resource->getLink('article')->getNCName());
        $this->assertNull($this->resource->getLink('article')->getReference());
        $this->assertEquals('article', $this->resource->getLink('article')->getName());

        $this->assertInstanceOf('Ekino\\HalClient\\Link', $this->resource->getLink('bar:tag'));

        $this->assertEquals('bar', $this->resource->getLink('bar:tag')->getNCName());
        $this->assertEquals('tag', $this->resource->getLink('bar:tag')->getReference());
        $this->assertEquals('bar:tag', $this->resource->getLink('bar:tag')->getName());
    }

    public function testGetCurie()
    {
        $this->assertInstanceOf('Ekino\\HalClient\\Curie', $this->resource->getCurie('test'));
        $this->assertNull($this->resource->getCurie('bar'));

        $this->assertEquals('test', $this->resource->getCurie('test')->getName());
        $this->assertEquals('http://localhost/path/to/docs/{rel}', $this->resource->getCurie('test')->getHref());
        $this->assertTrue($this->resource->getCurie('test')->isTemplated());
    }

    public function testGetDocs()
    {
        $this->assertEquals('http://localhost/path/to/docs/media', $this->resource->getCurieHref($this->resource->getLink('test:media')));

        $this->assertNull($this->resource->getCurieHref($this->resource->getLink('article')));

        $this->assertNull($this->resource->getCurieHref($this->resource->getLink('bar:tag')));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLinkGetFail()
    {
        $this->resource->getResource($this->resource->getLink('article'));
    }

    public function testLinkHref()
    {
        $this->assertEquals('http://localhost/article/42', $this->resource->getLink('article')->getHref(array('id' => 42)));

        $this->assertEquals('http://localhost/tag/{id}', $this->resource->getLink('bar:tag')->getHref(array('id' => 42)));

        $this->assertEquals('http://localhost/media', $this->resource->getLink('test:media')->getHref());
    }

    protected function setUp()
    {
        $this->client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');

        $this->resource = new Resource($this->client, array(), array(
            'curies' => array(
                array(
                    'name' => 'test',
                    'href' => 'http://localhost/path/to/docs/{rel}',
                    'templated' => true
                )
            ),
            'test:media' => array(
                'href' => 'http://localhost/media'
            ),
            'article' => array(
                'href' => 'http://localhost/article/{id}',
                'templated' => true
            ),
            'bar:tag' => array(
                'href' => 'http://localhost/tag/{id}',
                'templated' => false
            )
        ));
    }
}
