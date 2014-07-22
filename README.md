HalClient
=========

[![Build Status](https://secure.travis-ci.org/ekino/hal-client.png)](https://secure.travis-ci.org/#!/ekino/hal-client)

HalClient is a lightweight library to consume HAL resources.

### Installation using Composer

Add the dependency:

```bash
php composer.phar require ekino/hal-client
```

If asked for a version, type in 'dev-master' (unless you want another version):

```bash
Please provide a version constraint for the ekino/hal-client requirement: dev-master
```

### Usage

```php

<?php

// create a HttpClient to perform http request
$client = new FileGetContentsHttpClient(array(
    'Authorization' => 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ=='
));
$client->setBaseUrl('http://propilex.herokuapp.com');

// create an entry point to retrieve the data
$entryPoint = new EntryPoint('/', $client);
$resource = $entryPoint->get(); // return the main resource

// retrieve a Resource object, which acts as a Pager
$pager = $resource->get('p:documents');

$pager->get('page');

$collection = $pager->get('documents'); // return a ResourceCollection

// a ResourceCollection implements the \Iterator and \Countable interface
foreach ($collection as $document) {
    // the document is a resource object
    $document->get('title');
}

```


### Integrate JMS/Deserializer

The library support deserialization of Resource object into native PHP object.

```php

$serializerBuilder = SerializerBuilder::create();
$serializerBuilder->setDeserializationVisitor('hal', new ResourceDeserializationVisitor(new CamelCaseNamingStrategy()));
$serializerBuilder->configureHandlers(function($handlerRegistry) {
    $handlerRegistry->registerSubscribingHandler(new DateHandler());
    $handlerRegistry->registerSubscribingHandler(new ArrayCollectionHandler());
});

$serializer = $serializerBuilder->build();

$object = $serializer->deserialize($resource, 'Ekino\HalClient\Article', 'hal');

```