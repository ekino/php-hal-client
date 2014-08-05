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

class FileGetContentsHttpClient implements HttpClientInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $defaultHeaders;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * Constructor.
     *
     * @param string $baseUrl
     * @param array  $defaultHeaders
     * @param float  $timeout
     */
    public function __construct($baseUrl, array $defaultHeaders = array(), $timeout = 1.0)
    {
        $this->defaultHeaders = $defaultHeaders;
        $this->timeout = $timeout;

        // normalize
        if (substr($baseUrl, -1) !== '/') {
            $baseUrl .= '/';
        }

        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $headers
     * @param array  $data
     *
     * @return HttpResponse
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     */
    protected function doRequest($url, $method, array $headers, array $data)
    {
        $headers['Accept'] = 'application/hal+json';

        $opts = array(
            'http' => array(
                'method'  => strtoupper($method),
                'header'  => $this->buildHeaders(array_merge($this->defaultHeaders, $headers)),
                'timeout' => $this->timeout,

                // need to set configuration options
                'user_agent'      => 'Ekino HalClient v0.1',
                'follow_location' => 0,
                'max_redirects'   => 20,
            )
        );

        // if is relative url
        if ('http' !== substr($url, 0, 4)) {
            // clean
            if ($url[0] === '/') {
                $url = substr($url, 1);
            }

            $url = $this->baseUrl . $url;
        }

        // http://php.net/manual/en/reserved.variables.httpresponseheader.php
        $content = @file_get_contents($url, false, stream_context_create($opts));

        if (false === $content) {
            throw new RequestFailedException(sprintf("Couldn't reach `%s`; maybe something is wrong with the host?", $url));
        }

        if (empty($http_response_header)) {
            throw new \RuntimeException('Empty response, no headers');
        }

        $data = explode(" ", $http_response_header[0]);

        return new HttpResponse($data[1], $this->parseHeaders($http_response_header), $content);
    }

    /**
     * @param array $headers
     *
     * @return string
     */
    protected function buildHeaders(array $headers)
    {
        $data = "";
        foreach ($headers as $name => $value) {
            $data .= sprintf("%s: %s\r\n", $name, $value);
        }

        return $data;
    }

    /**
     * @param $lines
     *
     * @return array
     */
    protected function parseHeaders($lines)
    {
        $headers = array();
        foreach($lines as $line) {
            $data = explode(":", $line, 2);

            if (count($data) !== 2) {
                continue;
            }

            $headers[$data[0]] = trim($data[1]);
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, $headers = array())
    {
        return $this->doRequest($url, 'GET', $headers, array());
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $data = array(), $headers = array())
    {
        throw new \RuntimeException('Feature not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $data = array(), $headers = array())
    {
        throw new \RuntimeException('Feature not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, $headers = array())
    {
        throw new \RuntimeException('Feature not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url, $data = array(), $headers = array())
    {
        throw new \RuntimeException('Feature not implemented');
    }
}