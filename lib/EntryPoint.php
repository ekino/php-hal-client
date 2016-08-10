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

class EntryPoint
{
    protected $url;

    protected $headers;

    protected $client;

    /**
     * @var string Default content type
     */
    protected static $content_type = 'application/hal+json';

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @param string              $url
     * @param array               $headers
     * @param HttpClientInterface $client
     */
    public function __construct($url, HttpClientInterface $client, array $headers = array())
    {
        $this->url     = $url;
        $this->client  = $client;
        $this->headers = $headers;

        $this->resource = false;
    }

    /**
     * @param HttpResponse        $response
     * @param HttpClientInterface $client
     *
     * @return Resource
     *
     * @throws \RuntimeException
     */
    public static function parse(HttpResponse $response, HttpClientInterface $client)
    {
        if (substr($response->getHeader('Content-Type'), 0, 20) !== self::$content_type ) {
            throw new \RuntimeException('Invalid content type');
        }

        $data = @json_decode($response->getBody(), true);

        if ($data === null) {
            throw new \RuntimeException('Invalid JSON format');
        }

        return Resource::create($client, $data);
    }

    /**
     * @param string $name
     *
     * @return Resource
     */
    public function get($name = null)
    {
        $this->initialize();

        if ($name) {
            return $this->resource->get($name);
        }

        return $this->resource;
    }

    /**
     * Initialize the resource.
     */
    protected function initialize()
    {
        if ($this->resource) {
            return;
        }

        $this->resource = static::parse($this->client->get($this->url), $this->client);
    }

    /**
     * Set the default content type to check in parse method
     *
     * @param $content_type
     * @return bool
     * @todo Check if string is a valid content type
     */
    public static function setDefaultContentType ($content_type)
    {
        if ($content_type){
            self::$content_type = $content_type;
            return true;
        }
        return false;
    }
}
