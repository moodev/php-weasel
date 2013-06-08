<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Serialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ClassSerialization
{

    const INCLUDE_ALWAYS = 1;
    const INCLUDE_NON_DEFAULT = 2;
    const INCLUDE_NON_EMPTY = 3;
    const INCLUDE_NON_NULL = 4;

    /**
     * @var \Weasel\XmlMarshaller\Config\Serialization\PropertySerialization[]
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Serialization\PropertySerialization[string]")
     */
    public $properties = array();

    /**
     * @var int
     * @JsonProperty(type="int")
     */
    public $include = self::INCLUDE_ALWAYS;

    /**
     * @var \Weasel\XmlMarshaller\Config\Serialization\TypeInfo
     * @JsonProperty(type="\Weasel\XmlMarshaller\Config\Serialization\TypeInfo")
     */
    public $typeInfo;
}
