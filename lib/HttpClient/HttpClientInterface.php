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

use Ekino\HalClient\Exception\RequestException;

interface HttpClientInterface
{
    /**
     * @param string $url
     * @param array  $headers
     *
     * @return HttpResponse
     *
     * @throws RequestException
     */
    public function get($url, $headers = array());

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     *
     * @return HttpResponse
     *
     * @throws RequestException
     */
    public function post($url, $data = array(), $headers = array());

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     *
     * @return HttpResponse
     *
     * @throws RequestException
     */
    public function put($url, $data = array(), $headers = array());

    /**
     * @param string $url
     * @param array  $headers
     *
     * @return HttpResponse
     *
     * @throws RequestException
     */
    public function delete($url, $headers = array());

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     *
     * @return HttpResponse
     *
     * @throws RequestException
     */
    public function patch($url, $data = array(), $headers = array());
}