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

class Curie extends AbstractLink
{
    /**
     * Send a request.
     *
     * @param null|string $rel
     *
     * @return Resource
     *
     * @throws \InvalidArgumentException When the variable rel is necessary
     */
    public function get($rel = null)
    {
        if ($this->templated && null === $rel) {
            throw new \InvalidArgumentException('You forgot the rel.');
        }

        $entryPoint = new EntryPoint($this->prepareUrl(array('rel' => $rel)), $this->resource->getClient());

        return $entryPoint->get();
    }
}
