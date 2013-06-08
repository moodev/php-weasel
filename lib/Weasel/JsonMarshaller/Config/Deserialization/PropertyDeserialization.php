<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeInfo;

/**
 * Class PropertyDeserialization
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Deserialization\Param"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Deserialization\SetterDeserialization"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="method")
 */
abstract class PropertyDeserialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $type;

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\TypeInfo
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Deserialization\TypeInfo")
     */
    public $typeInfo;

    /**
     * @return string
     */
    abstract public function __toString();
}
