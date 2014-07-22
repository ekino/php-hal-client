<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient\Deserialization\Construction;

use Ekino\HalClient\Proxy\HalResourceEntityInterface;
use Ekino\HalClient\Resource;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Serializer;
use JMS\Serializer\VisitorInterface;

class ProxyObjectConstruction implements ObjectConstructorInterface
{
    protected $serializer;

    protected $pattern;

    /**
     * @param $pattern
     */
    public function __construct($pattern = "Proxy\\%s")
    {
        $this->pattern = $pattern;
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        $name = sprintf($this->pattern, $metadata->name);

        if (!class_exists($name, false)) {
            $name = $metadata->name;
        }

        $instance = unserialize(sprintf('O:%d:"%s":0:{}', strlen($name), $name));

        if ($instance instanceof HalResourceEntityInterface && $data instanceof Resource) {
            $instance->setHalResource($data);
            if ($this->serializer) {
                $instance->setHalSerializer($this->serializer);
            }
        }

        return $instance;
    }
}