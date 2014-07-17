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

$entryPoint = new EntryPoint('http://propilex.herokuapp.com', $client);
$resource = $entryPoint->get(); // return the main resource

$pager = $resource->get('p:documents');

$pager->get('page'); // return the page attribute

$collection = $pager->get('documents'); // return a ResourceCollection


```

