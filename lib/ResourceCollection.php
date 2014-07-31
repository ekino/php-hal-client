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

use Ekino\HalClient\HttpClient\HttpClientInterface;

class ResourceCollection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    protected $collection;

    protected $client;

    /**
     * @param HttpClientInterface $client
     * @param array               $collection
     */
    public function __construct(HttpClientInterface $client, array $collection = array())
    {
        $this->client     = $client;

        foreach ($collection as $pos => $data) {
            $collection[$pos] = Resource::create($client, $data);
        }

        $this->collection = $collection;

        $this->iterator = new \ArrayIterator($this->collection);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Operation not allowed');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Operation not allowed');
    }
}
