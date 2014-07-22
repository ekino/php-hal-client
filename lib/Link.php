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

class Link
{
    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var null|array
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $title;

    /**
     * @var null|string
     */
    protected $href;

    /**
     * @var null|bool
     */
    protected $templated = false;

    /**
     * Constructor.
     *
     * @param Resource $resource
     * @param array    $data
     */
    public function __construct(Resource $resource, array $data)
    {
        $this->resource = $resource;

        $this->title     = isset($data['title'])     ? $data['title'] : null;
        $this->href      = isset($data['href'])      ? $data['href'] : null;
        $this->templated = isset($data['templated']) ? (boolean) $data['templated'] : false;

        if (isset($data['name'])) {
            if (false !== strpos($data['name'], ':')) {
                $this->name = explode(':', $data['name'], 2);
            } else {
                $this->name = $data['name'];
            }
        }
    }

    /**
     * Send a request.
     *
     * @param array $variables Required if the link is templated
     *
     * @return HttpClient\HttpResponse
     *
     * @throws \RuntimeException         When call with property "href" empty
     * @throws \InvalidArgumentException When variables is required and is empty
     */
    public function get(array $variables = array())
    {
        if (null === $this->href) {
            throw new \RuntimeException('Href must to be sets.');
        }

        if (!$this->templated) {
            return $this->resource->getClient()->get($this->href);
        }

        if (empty($variables)) {
            throw new \InvalidArgumentException('You forgot the variables.');
        }

        $template = new UriTemplate();

        return $this->resource->getClient()->get($template->expand($this->href, $variables));
    }

    /**
     * Returns the href docs.
     *
     * @return null|string
     */
    public function getDocs()
    {
        if (!is_array($this->name)) {
            return null;
        }

        $curie = $this->resource->getCurie($this->name[0]);

        if (null === $curie) {
            return null;
        }

        if (!$curie->isTemplated()) {
            return $curie->getHref();
        }

        $template = new UriTemplate();

        return $template->expand($curie->getHref(), array('rel' => $this->name[1]));
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        if (is_array($this->name)) {
            return sprintf('%s:%s', $this->name[0], $this->name[1]);
        }

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
     * @return null|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function isTemplated()
    {
        return $this->templated;
    }
}
