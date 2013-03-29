<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;

class DirectSerialization extends PropertySerialization
{

    /**
     * @var string
     */
    public $property;

    public function __toString()
    {
        return "[DirectSerialization property={$this->property} include={$this->include} type={$this->type} typeInfo={$this->typeInfo}]";
    }

}
