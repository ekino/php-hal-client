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

interface HttpClientInterface
{
    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl);

    /**
     * @param string $url
     * @param array  $headers
     *
     * @return HttpResponse
     */
    public function get($url, $headers = array());

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     *
     * @return HttpResponse
     */
    public function post($url, $data = array(), $headers = array());

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     *
     * @return HttpResponse
     */
    public function put($url, $data = array(), $headers = array());

    /**
     * @param string $url
     * @param array  $headers
     *
     * @return HttpResponse
     */
    public function delete($url, $headers = array());

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     *
     * @return HttpResponse
     */
    public function patch($url, $data = array(), $headers = array());
}