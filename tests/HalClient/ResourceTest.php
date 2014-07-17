<?php

namespace Exporter\Test;

use Ekino\HalClient\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testHandler()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');

        $resource = new Resource($client);
    }
}
