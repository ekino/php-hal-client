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

class Resource
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
            $this->links[$name] = new Link($this->links[$name]);
        }

        return $this->links[$name];
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
            $this->curies[$curie['name']] = $curie;
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
     */
    protected function buildResourceValue($name)
    {
        $link = $this->getLink($name);

        if (!$link) {
            return false;
        }

        $response = $this->client->get($link->getHref());

        if (!$response instanceof HttpResponse) {
            throw new \RuntimeException(sprintf('HttpClient does not return a valid HttpResponse object, given: %s', $response));
        }

        $this->embedded[$name] = EntryPoint::parse($response, $this->client);

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
}