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
    public function __construct(array $data)
    {
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
     * Returns the href.
     *
     * @param array $variables Required if the link is templated
     *
     * @return null|string
     *
     * @throws \RuntimeException When call with property "href" empty and sets variables
     */
    public function getHref(array $variables = array())
    {
        if (!empty($variables)) {
            return $this->prepareUrl($variables);
        }

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
     * @throws \RuntimeException When call with property "href" empty
     */
    private function prepareUrl(array $variables = array())
    {
        if (null === $this->href) {
            throw new \RuntimeException('Href must to be sets.');
        }

        if (!$this->templated) {
            return $this->href;
        }

        $template = new UriTemplate();

        return $template->expand($this->href, $variables);
    }
}
