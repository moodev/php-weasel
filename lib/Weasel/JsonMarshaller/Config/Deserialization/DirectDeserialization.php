<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

class DirectDeserialization extends PropertyDeserialization
{

    /**
     * @var string
     */
    public $property;

    public function __toString()
    {
        return "[DirectDeserialization property={$this->property} type={$this->type} typeInfo={$this->typeInfo}]";
    }
}
