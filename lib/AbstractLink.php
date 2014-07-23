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

use Guzzle\Parser\UriTemplate\UriTemplate;

abstract class AbstractLink
{
    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var string
     *
     * @see http://www.w3.org/TR/1999/REC-xml-names-19990114/#NT-NCName
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $href;

    /**
     * @var bool
     */
    protected $templated = false;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(Resource $resource, array $data)
    {
        $this->resource = $resource;

        $this->name      = isset($data['name'])      ? $data['name'] : null;
        $this->href      = isset($data['href'])      ? $data['href'] : null;
        $this->templated = isset($data['templated']) ? (boolean) $data['templated'] : false;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return bool
     */
    public function isTemplated()
    {
        return $this->templated;
    }

    /**
     * Prepare the url with variables.
     *
     * @param array $variables Required if the link is templated
     *
     * @return string
     *
     * @throws \RuntimeException         When call with property "href" empty
     * @throws \InvalidArgumentException When variables is required and is empty
     */
    public function prepareUrl(array $variables = array())
    {
        if (null === $this->href) {
            throw new \RuntimeException('Href must to be sets.');
        }

        if (!$this->templated) {
            return $this->href;
        }

        if (empty($variables)) {
            throw new \InvalidArgumentException('You forgot the variables.');
        }

        $template = new UriTemplate();

        return $template->expand($this->href, $variables);
    }
}
