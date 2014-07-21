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
use JMS\Serializer\Handler\DateHandler as NativeDateHandler;

class DateHandler extends NativeDateHandler
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $types = array('DateTime', 'DateInterval');

        foreach (array('hal') as $format) {
            $methods[] = array(
                'type' => 'DateTime',
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => $format,
            );
        }

        return $methods;
    }
}
