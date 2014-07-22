<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient\Proxy;

use Ekino\HalClient\Resource;
use JMS\Serializer\Serializer;

trait HalResourceEntity
{
    /**
     * @var array
     */
    protected $__halResourceLoaded = array();

    /**
     * @var Resource
     */
    protected $__halResource;

    /**
     * @var Serializer
     */
    protected $__halSerializer;

    /**
     * @param array $halResourceLoaded
     */
    public function setHalResourceLoaded($halResourceLoaded)
    {
        $this->__halResourceLoaded = $halResourceLoaded;
    }

    /**
     * @return array
     */
    public function getHalResourceLoaded()
    {
        return $this->__halResourceLoaded;
    }

    /**
     * @param mixed $halSerializer
     */
    public function setHalSerializer(Serializer $halSerializer)
    {
        $this->__halSerializer = $halSerializer;
    }

    /**
     * @return mixed
     */
    public function getHalSerializer()
    {
        return $this->__halSerializer;
    }

    /**
     * @param Resource $halResource
     */
    public function setHalResource(Resource $halResource)
    {
        $this->__halResource = $halResource;
    }

    /**
     * @return Resource
     */
    public function getHalResource()
    {
        return $this->__halResource;
    }

    /**
     * @param $name
     */
    public function halLoaded($name)
    {
        $this->__halResourceLoaded[$name] = true;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function halIsLoaded($name)
    {
        return isset($this->__halResourceLoaded[$name]);
    }
}

