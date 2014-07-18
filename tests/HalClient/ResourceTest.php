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

use Ekino\HalClient\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testHandler()
    {
        $client = $this->getMock('Ekino\HalClient\HttpClient\HttpClientInterface');

        $resource = new Resource($client);
    }
}
