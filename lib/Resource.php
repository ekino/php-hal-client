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
use Ekino\HalClient\HttpClient\HttpResponse;

class Resource implements \ArrayAccess
{
    protected $properties;

    protected $links;

    protected $embedded;

    protected $curies;

    protected $client;

    /**
     * @param array               $properties
     * @param array               $links
     * @param array               $embedded
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client, $properties = array(), $links = array(), $embedded = array())
    {
        $this->client     = $client;
        $this->properties = $properties;
        $this->links      = $links;
        $this->embedded   = $embedded;

        $this->parseCuries();
    }

    /**
     * @return HttpClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getEmbedded()
    {
        return $this->embedded;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Reloads the Resource by using the self reference
     *
     * @throws \RuntimeException
     */
    public function refresh()
    {
        $link = $this->getLink('self');

        if (!$link) {
            throw new \RuntimeException('Invalid resource, not `self` reference available');
        }

        $r = $this->getResource($link);

        $this->properties = $r->getProperties();
        $this->links      = $r->getLinks();
        $this->embedded   = $r->getEmbedded();

        $this->parseCuries();
    }

    /**
     * @param $name
     *
     * @return Link
     */
    public function getLink($name)
    {
        if (!array_key_exists($name, $this->links)) {
            return null;
        }

        if (!$this->links[$name] instanceof Link) {
            $this->links[$name] = new Link($this, array_merge(array('name' => $name), $this->links[$name]));
        }

        return $this->links[$name];
    }

    /**
     * @param $name
     *
     * @return Link
     */
    public function getCurie($name)
    {
        if (!array_key_exists($name, $this->curies)) {
            return null;
        }

        return $this->curies[$name];
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    protected function parseCuries()
    {
        $this->curies = array();

        if (!array_key_exists('curies', $this->links)) {
            return;
        }

        foreach ($this->links['curies'] as $curie) {
            $this->curies[$curie['name']] = new Link($this, $curie);
        }
    }

    /**
     * @param $name
     *
     * @return Resource|ResourceCollection|null
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        if (!array_key_exists($name, $this->embedded)) {
            if (!$this->buildResourceValue($name)) {
                return null;
            }
        }

        return $this->getEmbeddedValue($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->hasProperty($name) || $this->hasLink($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasLink($name)
    {
        return isset($this->links[$name]);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasEmbedded($name)
    {
        return isset($this->embedded[$name]);
    }

    /**
     * @param $name
     *
     * @return boolean
     */
    protected function buildResourceValue($name)
    {
        $link = $this->getLink($name);

        if (!$link) {
            return false;
        }

        $this->embedded[$name] = $this->getResource($link);

        return true;
    }

    /**
     * @param $name
     *
     * @return Resource|ResourceCollection
     */
    protected function getEmbeddedValue($name)
    {
        if ( !is_object($this->embedded[$name])) {
            if (is_integer(key($this->embedded[$name])) || empty($this->embedded[$name])) {
                $this->embedded[$name] = new ResourceCollection($this->client, $this->embedded[$name]);
            } else {
                $this->embedded[$name] = self::create($this->client, $this->embedded[$name]);
            }
        }

        return $this->embedded[$name];
    }

    /**
     * @param HttpClientInterface $client
     * @param array               $data
     *
     * @return Resource
     */
    public static function create(HttpClientInterface $client, array $data)
    {
        $links    = isset($data['_links']) ? $data['_links'] : array();
        $embedded = isset($data['_embedded']) ? $data['_embedded'] : array();

        unset(
            $data['_links'],
            $data['_embedded']
        );

        return new self($client, $data, $links, $embedded);
    }

    /**
     * @param Link $link
     *
     * @return Resource
     * @throws \RuntimeException
     */
    private function getResource(Link $link)
    {
        $response = $this->client->get($link->getHref());

        if (!$response instanceof HttpResponse) {
            throw new \RuntimeException(sprintf('HttpClient does not return a valid HttpResponse object, given: %s', $response));
        }

        if ($response->getStatus() !== 200) {
            throw new \RuntimeException(sprintf('HttpClient does not return a status code, given: %s', $response->getStatus()));
        }

        return EntryPoint::parse($response, $this->client);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
       return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Operation not available');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Operation not available');
    }
}