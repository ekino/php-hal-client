<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient\Deserialization;

use Ekino\HalClient\Deserialization\Handler\ArrayCollectionHandler;
use Ekino\HalClient\Deserialization\Handler\DateHandler;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

final class Builder
{
    private function __construct()
    {}

    /**
     * @param bool $autoload
     * @param bool $cacheDir
     *
     * @return SerializerBuilder
     */
    static function get($autoload = true, $cacheDir = false)
    {
        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->setDeserializationVisitor('hal', new ResourceDeserializationVisitor(new CamelCaseNamingStrategy(), $autoload));
        $serializerBuilder->configureHandlers(function($handlerRegistry) {
            $handlerRegistry->registerSubscribingHandler(new DateHandler());
            $handlerRegistry->registerSubscribingHandler(new ArrayCollectionHandler());
        });

        if ($cacheDir) {
            $serializerBuilder->setCacheDir($cacheDir);
        }

        return $serializerBuilder;
    }

    /**
     * @param bool $autoload
     * @param bool $cacheDir
     *
     * @return Serializer
     */
    static function build($autoload = true, $cacheDir = false)
    {
        return self::get($autoload, $cacheDir)->build();
    }
}