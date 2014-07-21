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

use Ekino\HalClient\Resource;
use Ekino\HalClient\ResourceCollection;
use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\Context;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\ClassMetadata;

class ResourceDeserializationVisitor extends AbstractVisitor
{
    private $navigator;
    private $result;
    private $objectStack;
    private $currentObject;

    /**
     * {@inheritdoc}
     */
    protected function decode($str)
    {
        if (!$str instanceof Resource) {
            throw new \RuntimeException('Invalid argument, Ekino\HalClient\Resource required');
        }

        return $str;
    }

    /**
     * {@inheritdoc}
     */
    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        $name = $this->namingStrategy->translateName($metadata);

        if (isset($data[$name]) === false) {
            return;
        }

        if ( ! $metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->reflection->class, $metadata->name));
        }

        $v = $data[$name] !== null ? $this->getNavigator()->accept($data[$name], $metadata->type, $context) : null;

        if (null === $metadata->setter) {
            $metadata->reflection->setValue($this->getCurrentObject(), $v);

            return;
        }

        $this->getCurrentObject()->{$metadata->setter}($v);
    }

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->result = null;
        $this->objectStack = new \SplStack;
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function prepare($data)
    {
        return $this->decode($data);
    }

    public function visitNull($data, array $type, Context $context)
    {
        return null;
    }

    public function visitString($data, array $type, Context $context)
    {
        $data = (string) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        $data = (Boolean) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitInteger($data, array $type, Context $context)
    {
        $data = (integer) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitDouble($data, array $type, Context $context)
    {
        $data = (double) $data;

        if (null === $this->result) {
            $this->result = $data;
        }

        return $data;
    }

    public function visitArray($data, array $type, Context $context)
    {
        if ( ! $data instanceof ResourceCollection && !is_array($data) ) {
            throw new RuntimeException(sprintf('Expected ResourceCollection or array, but got %s: %s', gettype($data), json_encode($data)));
        }

        // If no further parameters were given, keys/values are just passed as is.
        if ( ! $type['params']) {
            if (null === $this->result) {
                $this->result = $data;
            }

            return $data;
        }

        switch (count($type['params'])) {
            case 1: // Array is a list.
                $listType = $type['params'][0];

                $result = array();
                if (null === $this->result) {
                    $this->result = &$result;
                }

                foreach ($data as $v) {
                    $result[] = $this->navigator->accept($v, $listType, $context);
                }

                return $result;

            case 2: // Array is a map.
                list($keyType, $entryType) = $type['params'];

                $result = array();
                if (null === $this->result) {
                    $this->result = &$result;
                }

                foreach ($data as $k => $v) {
                    $result[$this->navigator->accept($k, $keyType, $context)] = $this->navigator->accept($v, $entryType, $context);
                }

                return $result;

            default:
                throw new RuntimeException(sprintf('Array type cannot have more than 2 parameters, but got %s.', json_encode($type['params'])));
        }
    }

    public function startVisitingObject(ClassMetadata $metadata, $object, array $type, Context $context)
    {
        $this->setCurrentObject($object);

        if (null === $this->result) {
            $this->result = $this->currentObject;
        }
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        $obj = $this->currentObject;
        $this->revertCurrentObject();

        return $obj;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setCurrentObject($object)
    {
        $this->objectStack->push($this->currentObject);
        $this->currentObject = $object;
    }

    public function getCurrentObject()
    {
        return $this->currentObject;
    }

    public function revertCurrentObject()
    {
        return $this->currentObject = $this->objectStack->pop();
    }

}