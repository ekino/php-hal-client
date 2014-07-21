<?php

/*
* This file is part of the Ekino HalClient package.
*
* (c) 2014 Ekino
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ekino\HalClient\Deserialization\Handler;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\ArrayCollectionHandler as NativeArrayCollectionHandler;

class ArrayCollectionHandler extends NativeArrayCollectionHandler
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('hal');
        $collectionTypes = array(
            'ArrayCollection',
            'Doctrine\Common\Collections\ArrayCollection',
            'Doctrine\ORM\PersistentCollection',
            'Doctrine\ODM\MongoDB\PersistentCollection',
            'Doctrine\ODM\PHPCR\PersistentCollection',
        );

        foreach ($collectionTypes as $type) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserializeCollection',
                );
            }
        }

        return $methods;
    }
}
