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

class Link
{
    protected $title;

    protected $href;

    /**
     * @param $data
     */
    public function __construct(array $data)
    {
        $this->title = isset($data['title']) ? $data['title'] : null;
        $this->href  = isset($data['href']) ? $data['href'] : null;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}