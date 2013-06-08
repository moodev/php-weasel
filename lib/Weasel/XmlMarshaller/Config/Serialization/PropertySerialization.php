<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Serialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeInfo;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;

/**
 * Class PropertySerialization
 * @package Weasel\XmlMarshaller\Config\Serialization
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\XmlMarshaller\Config\Serialization\GetterSerialization"),
 * @JsonSubTypes\Type("\Weasel\XmlMarshaller\Config\Serialization\DirectSerialization"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="how")
 */
class PropertySerialization
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
     * @var \Weasel\XmlMarshaller\Config\Serialization\TypeInfo
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Serialization\TypeInfo")
     */
    public $typeInfo;
}
