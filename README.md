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

// create an entry point to retrieve the data
$entryPoint = new EntryPoint('http://propilex.herokuapp.com', $client);
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

