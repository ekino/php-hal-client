<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient\Deserialization {

    use Ekino\HalClient\Deserialization\Construction\ProxyObjectConstruction;
    use Ekino\HalClient\Deserialization\Handler\ArrayCollectionHandler;
    use Ekino\HalClient\Deserialization\Handler\DateHandler;
    use Ekino\HalClient\Deserialization\ResourceDeserializationVisitor;
    use Ekino\HalClient\HttpClient\HttpResponse;
    use Ekino\HalClient\Resource;
    use JMS\Serializer\Naming\CamelCaseNamingStrategy;
    use JMS\Serializer\SerializerBuilder;
    use Doctrine\Common\Collections\ArrayCollection;

    class DeserializationTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @return SerializerBuilder
         */
        public function getSerializerBuilder($autoload = true)
        {
            $serializerBuilder = SerializerBuilder::create();
            $serializerBuilder->setDeserializationVisitor('hal', new ResourceDeserializationVisitor(new CamelCaseNamingStrategy(), $autoload));
            $serializerBuilder->configureHandlers(function($handlerRegistry) {
                $handlerRegistry->registerSubscribingHandler(new DateHandler());
                $handlerRegistry->registerSubscribingHandler(new ArrayCollectionHandler());
            });

            return $serializerBuilder;
        }

        /**
         * @return Resource
         */
        public function getResource()
        {
            $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');
             $client->expects($this->exactly(1))->method('get')->will($this->returnCallback(function($url) {
                 if ($url == '/users/1') {
                     return new HttpResponse(200, array(
                         'Content-Type' => 'application/hal+json'
                     ), json_encode(array(
                         'name' => 'Thomas Rabaix',
                         'email' => 'thomas.rabaix@ekino.com'
                     )));
                 }
             }));

             $resource = new Resource($client, array(
                 'name' => 'Salut',
             ), array(
                 'fragments' => array('href' => '/document/1/fragments'),
                 'author' => array('href' => '/users/1')
             ), array(
                 'fragments' => array(
                     array(
                         'type' => 'test',
                         'settings' => array(
                             'color' => 'red'
                         )
                     ),
                     array(
                         'type' => 'image',
                         'settings' => array(
                             'url' => 'http://dummyimage.com/600x400/000/fff'
                         )
                     )
                 )
             ));

            return $resource;
        }

        public function testMapping()
        {
            $serializerBuilder = $this->getSerializerBuilder();

            $resource = $this->getResource();
            $serializer = $serializerBuilder->build();

            $object = $serializer->deserialize($resource, 'Ekino\HalClient\Deserialization\Article', 'hal');

            $this->assertEquals('Salut', $object->getName());

            $fragments = $object->getFragments();
            $this->assertCount(2, $fragments);
            $this->assertEquals($fragments[0]->getType(), 'test');
            $this->assertEquals($fragments[1]->getType(), 'image');
            $this->assertNotNull($object->getAuthor());
            $this->assertEquals($object->getAuthor()->getEmail(), 'thomas.rabaix@ekino.com');
        }

        public function testWithValidProxy()
        {
            $serializerBuilder = $this->getSerializerBuilder(false);

            $constructor = new ProxyObjectConstruction('Proxy\%s');

            // todo, inject proxy handler
            $serializerBuilder->setObjectConstructor($constructor);

            $resource = $this->getResource();
            $serializer = $serializerBuilder->build();

            $constructor->setSerializer($serializer);

            $object = $serializer->deserialize($resource, 'Ekino\HalClient\Deserialization\Article', 'hal');

            $this->assertInstanceOf('Proxy\Ekino\HalClient\Deserialization\Article', $object);
            $this->assertInstanceOf('Ekino\HalClient\Deserialization\Article', $object);
            $this->assertEquals('Salut', $object->getName());

            $fragments = $object->getFragments();

            $this->assertCount(2, $fragments);
            $this->assertEquals($fragments[0]->getType(), 'test');
            $this->assertEquals($fragments[1]->getType(), 'image');

            $this->assertInstanceOf('Proxy\Ekino\HalClient\Deserialization\Author', $object->getAuthor());
            $this->assertInstanceOf('Ekino\HalClient\Deserialization\Author', $object->getAuthor());

            $this->assertEquals($object->getAuthor()->getEmail(), 'thomas.rabaix@ekino.com');
        }
    }
}

namespace Proxy\Ekino\HalClient\Deserialization {

    use Ekino\HalClient\Proxy\HalResourceEntity;
    use Ekino\HalClient\Proxy\HalResourceEntityInterface;
    use Ekino\HalClient\Resource;

    class Article extends \Ekino\HalClient\Deserialization\Article implements HalResourceEntityInterface
    {
        use HalResourceEntity;

        /**
         * @return Author
         */
        public function getAuthor()
        {
            if (!$this->halIsLoaded('author')) {
                $this->halLoaded('author');

                $resource = $this->getHalResource()->get('author');

                if ($resource instanceof Resource) {
                    $this->author = $this->getHalSerializer()->deserialize($resource, 'Ekino\HalClient\Deserialization\Author', 'hal');
                }
            }

            return $this->author;
        }
    }

    class Author extends \Ekino\HalClient\Deserialization\Author implements HalResourceEntityInterface
    {
        use HalResourceEntity;
    }
}

namespace Ekino\HalClient\Deserialization {
    use JMS\Serializer\Annotation as Serializer;

    class Article
    {
        /**
         * @Serializer\Type("string")
         *
         * @var string
         */
        protected $name;

        /**
         * @Serializer\Type("Doctrine\Common\Collections\ArrayCollection<Ekino\HalClient\Deserialization\Fragment>")
         *
         * @var array
         */
        protected $fragments;

        /**
         * @param array $author
         */
        public function setAuthor(Author $author)
        {
            $this->author = $author;
        }

        /**
         * @return array
         */
        public function getAuthor()
        {
            return $this->author;
        }

        /**
         * @Serializer\Type("Ekino\HalClient\Deserialization\Author")
         *
         * @var array
         */
        protected $author;

        /**
         * @param array $fragments
         */
        public function setFragments(ArrayCollection $fragments)
        {
            $this->fragments = $fragments;
        }

        /**
         * @return array
         */
        public function getFragments()
        {
            return $this->fragments;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->name = $name;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }
    }

    class Fragment
    {
        /**
         * @Serializer\Type("string")
         *
         * @var string
         */
        protected $type;

        /**
         * @Serializer\Type("array")
         *
         * @var array
         */
        protected $settings;

        /**
         * @param array $settings
         */
        public function setSettings($settings)
        {
            $this->settings = $settings;
        }

        /**
         * @return array
         */
        public function getSettings()
        {
            return $this->settings;
        }

        /**
         * @param string $type
         */
        public function setType($type)
        {
            $this->type = $type;
        }

        /**
         * @return string
         */
        public function getType()
        {
            return $this->type;
        }
    }

    class Author
    {
        /**
         * @Serializer\Type("string")
         *
         * @var string
         */
        protected $name;

        /**
         * @Serializer\Type("string")
         *
         * @var string
         */
        protected $email;

        /**
         * @param string $email
         */
        public function setEmail($email)
        {
            $this->email = $email;
        }

        /**
         * @return string
         */
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->name = $name;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }
    }
}