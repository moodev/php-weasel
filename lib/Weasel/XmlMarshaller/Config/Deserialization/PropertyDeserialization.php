<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;

/**
 * Class PropertyDeserialization
 * @package Weasel\XmlMarshaller\Config\Deserialization
 * @JsonSubTypes({
 * @JsonSubTypes\Type("\Weasel\XmlMarshaller\Config\Deserialization\Param"),
 * @JsonSubTypes\Type("\Weasel\XmlMarshaller\Config\Deserialization\SetterDeserialization"),
 * @JsonSubTypes\Type("\Weasel\XmlMarshaller\Config\Deserialization\DirectDeserialization"),
 * })
 * @JsonTypeInfo(use=JsonTypeInfo::ID_NAME, include=JsonTypeInfo::AS_PROPERTY, property="how")
 */
abstract class PropertyDeserialization
{

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $id;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $type;

}
