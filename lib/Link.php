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

class Link extends AbstractLink
{
    /**
     * Prefix curie if the name is a curie.
     * Relation to curie name.
     *
     * @var null|string
     *
     * @see http://www.w3.org/TR/curie/#s_syntax
     */
    protected $ncName;

    /**
     * @var null|string
     */
    protected $reference;

    /**
     * @var null|string
     */
    protected $title;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->title = isset($data['title']) ? $data['title'] : null;

        if (null !== $this->name && false !== strpos($this->name, ':')) {
            list($this->ncName, $this->reference) = explode(':', $this->name, 2);
        }
    }

    /**
     * @return null|string
     */
    public function getNCName()
    {
        return $this->ncName;
    }

    /**
     * @return null|string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return null|string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
