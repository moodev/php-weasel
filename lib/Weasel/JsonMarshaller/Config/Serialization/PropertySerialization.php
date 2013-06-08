<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeInfo;

/**
 * Class PropertySerialization
 * @package Weasel\JsonMarshaller\Config\Serialization
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Serialization\DirectSerialization"),
 * @JsonSubTypes\Type("\Weasel\JsonMarshaller\Config\Serialization\GetterSerialization"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="how")
 */
abstract class PropertySerialization
{

    /**
     * @var integer
     * @JsonProperty(type="int")
     */
    public $include;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $type;

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\TypeInfo
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Serialization\TypeInfo")
     */
    public $typeInfo;

    /**
     * @return string
     */
    abstract public function __toString();
}
