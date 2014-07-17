<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient\HttpClient;

class HttpResponse
{
    protected $headers;

    protected $body;

    /**
     * @param array  $headers
     * @param string $body
     */
    public function __construct(array $headers, $body = '')
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getHeader($name, $default = null)
    {
        if (!array_key_exists($name, $this->headers)) {
            return $default;
        }

        return $this->headers[$name];
    }
}