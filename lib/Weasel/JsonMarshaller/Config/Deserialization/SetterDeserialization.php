<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

class SetterDeserialization extends PropertyDeserialization
{

    /**
     * @var string
     */
    public $method;

    public function __toString()
    {
        return "[SetterDeserialization method={$this->method} type={$this->type} typeInfo={$this->typeInfo}]";
    }

}
