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
 * Class Param
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonTypeName("direct")
 */
class Param extends PropertyDeserialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $name;

    public function __toString()
    {
        return "[ParamDeserialization name={$this->name} type={$this->type} typeInfo={$this->typeInfo}]";
    }

}
