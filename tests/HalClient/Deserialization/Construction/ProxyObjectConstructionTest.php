<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient\Deserialization\Construction
{
    use Ekino\HalClient\Deserialization\Construction\ProxyObjectConstruction;
    use Ekino\HalClient\Resource;
    use JMS\Serializer\DeserializationContext;
    use JMS\Serializer\Metadata\ClassMetadata;

    class ProxyObjectConstructionTest extends \PHPUnit_Framework_TestCase
    {
        public function testConstructorWithProxy()
        {
            $visitor = $this->getMock('JMS\Serializer\VisitorInterface');
            $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');

            $context = new DeserializationContext();
            $resource = new Resource($client);

            $constructor = new ProxyObjectConstruction();
            $object = $constructor->construct($visitor, new ClassMetadata('Acme\Post'), $resource, array(), $context);

            $this->assertInstanceOf('Ekino\HalClient\Proxy\HalResourceEntityInterface', $object);
            $this->assertInstanceOf('Ekino\HalClient\Resource', $object->getHalResource());
        }

        public function testConstructorWithoutProxy()
        {
            $visitor = $this->getMock('JMS\Serializer\VisitorInterface');
            $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');
            $context = new DeserializationContext();
            $resource = new Resource($client);

            $constructor = new ProxyObjectConstruction();
            $object = $constructor->construct($visitor, new ClassMetadata('Acme\NoProxy'), $resource, array(), $context);

            $this->assertNotInstanceOf('Ekino\HalClient\Proxy\HalResourceEntityInterface', $object);
            $this->assertInstanceOf('Acme\NoProxy', $object);
        }
    }
}

namespace Acme
{
    class Post {}

    class NoProxy {}
}

namespace Proxy\Acme
{
    use Ekino\HalClient\Proxy\HalResourceEntity;
    use Ekino\HalClient\Proxy\HalResourceEntityInterface;

    class Post extends \Acme\Post implements HalResourceEntityInterface
    {
        use HalResourceEntity;
    }
}

