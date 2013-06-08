<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class SetterDeserialization
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonTypeName("setter")
 */
class SetterDeserialization extends PropertyDeserialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $method;

    public function __toString()
    {
        return "[SetterDeserialization method={$this->method} type={$this->type} typeInfo={$this->typeInfo}]";
    }

}
